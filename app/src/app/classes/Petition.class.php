<?php
class Petition {
    // Database Module
    public const DB_TABLE = DB::TBL_PETS;
    public static function Create($title, $body, $img, $tags, $creator) {
        $title = protectInput(sanitize_string($title));
        if(is_array($creator)) $creator = json_encode($creator);
        if(is_array($tags)) $tags = json_encode($tags);
        $body = protectInput($body);
        $code = self::GenerateCode();
        $op = DB::Create(self::DB_TABLE, ['code', 'title', 'body', 'img', 'tags', 'creator', 'creation_time', 'update_time'], [$code, $title, $body, $img, $tags, $creator, time(), time()]);
        if($op[0]) {
            return [$op[0], self::Read('id', $op[1])['code']];
        }
        return false;
    }
    public static function Update($id, $title, $body, $tags, $status, $verified, $img=null) {
        $c = ['title', 'body', 'tags', 'status', 'verified', 'update_time'];
        $v = [$title, $body, $tags, $status, $verified, time()];
        if($img) {
            $c[] = 'img';
            $v[] = $img;
        }
        return DB::Update(self::DB_TABLE, $c, $v, ['id'], [$id]);
    }
    public static function PatchUpdate($id, $keys, $vals) {
        return DB::Update(self::DB_TABLE, $keys, $vals, ['id'], [$id]);
    }
    public static function Read($key, $val) {
        return DB::Read(self::DB_TABLE, $key, $val);
    }
    public static function Delete($id) {
        return DB::Delete(self::DB_TABLE, ['id'], [$id]);
    }
    public static function ReadRandom($count) {
        $stmt = DB::ExecuteQuery("SELECT * FROM ".self::DB_TABLE." WHERE status = ? AND verified = ? ORDER BY RAND() LIMIT ?", [0, 1, $count], PDO::PARAM_INT);
        if($stmt !== false) return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [];
    }
    public static function ReadPopularRandom($count) {
        $petitions = [];
        $retval = [];
        $stmt = DB::ExecuteQuery("SELECT * FROM ".self::DB_TABLE." WHERE status = ? AND verified = ?", [0, 1], PDO::PARAM_INT);
        if($stmt !== false) $petitions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($petitions) {
            $max = 0;
            $i = 0;
            $popular_petitions = [];
            $popular_petitions_full = [];
            foreach($petitions as $petition) {
                if(count($popular_petitions_full) >= $count) break;
                $signatures = Signatures::ReadAll('petition_id', $petition['id']);
                $signs_arr[$i] = [
                    'signatures' => count($signatures),
                    'petition' => $petition
                ];
                if($signs_arr[$i]['signatures'] > $max) {
                    $max = count($signatures);
                    $popular_petitions_full[] = $signs_arr[$i];
                }
                $i++;
            }
            if($popular_petitions_full) {
                usort($popular_petitions_full, function($a, $b) {
                    return $a['signatures'] <=> $b['signatures'];
                });
                foreach(array_reverse($popular_petitions_full) as $petition_full) {
                    $popular_petitions[] = $petition_full['petition'];
                }
            }
            $retval = $popular_petitions;
        }
        return $retval;
    }
    // Drivers
    public static function CreateDriver($title, $body, $img_data, $tags, $creator_name, $creator_email, $creator_country) {
        $image_op = Image::Create($img_data);
        if($image_op[0]) {
            $img_id = $image_op[1];
            $petition_creator = self::SetupCreator(protectInput(sanitize_string($creator_name)), $creator_email, Country::$country_array[$creator_country], null);
            return self::Create($title, $body, $img_id, $tags, $petition_creator);
        }
        return [false, null];
    }
    // Misc
    public const CODE_LEN = 8;
    public static function GenerateCode() {
        return random_sstr(self::CODE_LEN);
    }
    public static function SetupCreator($name, $email, $country, $extra=null) {
        $a = [
            'name' => $name,
            'email' => $email,
            'country' => $country
        ];
        if(is_array($extra)) {
            $a = array_merge($a, $extra);
        }
        return $a;
    }
    public static function GetCreator($creatorObj) {
        return json_decode($creatorObj, true);
    }
    public static function GetTags($tagsObj) {
        return json_decode($tagsObj, true);
    }
    // main
    public static function CanPetitionReceiveSignatures($petition_code) {
        $petition = self::Read('code', $petition_code);
        if(!$petition) return false;
        if(!$petition['verified']) return false;
        if(intval($petition['status']) !== 0) return false;
        return true;
    }
    public static function GetSignees($petition_id) {
        $petition = self::Read('id', $petition_id);
        $result = [];
        if($petition) {
            $signatures = Signatures::ReadAll('petition_id', $petition['id']);
            foreach($signatures as $signature) {
                $signee = json_decode($signature['signee'], true);
                $result[] = [
                    'signee' => $signee,
                    'verified' => $signature['verified']
                ];
            }
        }
        return $result;
    }
    public static function GetVerifiedSignatures($petition_id) {
        $all = self::GetSignees($petition_id);
        $result = [];
        foreach($all as $single) {
            if(intval($single['verified']) > 0) $result[] = $single;
        }
        return $result;
    }
    public static function IsSigneeSigned($petition_id, $signee_email) {
        $signees = self::GetVerifiedSignatures($petition_id);
        foreach($signees as $signee) {
            if($signee['signee']['email'] === $signee_email) {
                return true;
            }
        }
        return false;
    }
    public static function GenerateVerificationCode($petition) {
        return hash('ripemd128', json_encode($petition));
    }
    public static function ValidateVerificationCode($petition, $verification_code) {
        return self::GenerateVerificationCode($petition) === $verification_code;
    }
    public static function VerifyPetition($petition_id) {
        return self::PatchUpdate($petition_id, 'verified', 1);
    }
    // html
    public static function _($key) {
        $lang = load_requested_lang();
        return $lang->petition_view->$key;
    }
    public static function __($key) {
        $lang = load_requested_lang();
        return $lang->sign_form->$key;
    }
    public static function ___($key) {
        $lang = load_requested_lang();
        return $lang->petition_form->$key;
    }
    public static function showSignForm($petition_code) {
        if(!self::CanPetitionReceiveSignatures($petition_code)) return;
        $lang = load_requested_lang();
        echo '<div id="full-sign-petition--result" style="display:none;"></div>';
        echo '<div id="sign-verification-code-area" class="my-3" style="display:none;">';
            echo '<div class="text-center">
                <div class="alert alert-warning p-4">
                    <h3 class="mb-3"><i class="fas fa-exclamation-triangle"></i> '.$lang->sign_form->waiting_verify.'</h3>
                    <p><i class="fas fa-envelope-open-text"></i> '.$lang->sign_form->check_email_to_verify.'</p>
                    <div class="petition-full--verification-box text-center" id="sign-verification-box">
                        <div class="input-group flex-nowrap mb-2">
                            <label class="input-group-text" for="input-sign-verification-code">'.self::___('verification_code').'</label>
                            <input type="text" id="input-sign-verification-code" placeholder="'.self::___('verification_code').'" class="form-control" style="direction:ltr;">
                        </div>
                        <div class="flex-nowrap text-center">
                            <div id="sign-verification-box-processing"></div>
                            <button id="sign-verification-btn" class="btn btn-lg btn-primary"><i class="far fa-check-circle"></i> '.$lang->verify.'</button>
                        </div>
                    </div>
                </div>
            </div>';
            echo "<script>
            $('#sign-verification-box-processing').html(spinnerLoading);
            $('#sign-verification-box-processing').hide('fast');
            $('#sign-verification-btn').click((ev) => {
                ev.preventDefault();
                API('verify-signature', 'patch', {
                    'signature_id': $('#input-signature-id').val(),
                    'verification_code': $('#input-sign-verification-code').val()
                }, () => {
                    $('#sign-verification-box-processing').show('slow');
                    $('#sign-verification-btn').hide('slow');
                }, (response) => {
                    console.log(response);
                    if(response.success) {
                        ShowAlertResponse('#sign-verification-box-processing', true, '".$lang->sign_form->verification_process_done."', response.message);
                        appendSignedPetition($('#input-code').val());
                    } else {
                        ShowAlertResponse('#sign-verification-box-processing', false, '".self::___('verification_process_failed')."', response.message);
                        $('#sign-verification-btn').show('slow');
                    }
                });
            });
            </script>";
        echo '</div>';
        echo '
        <div id="full-sign-petition">
            <h4 class="card-text mb-3 text-danger">'.self::__('sign_head').'</h4>
            <div class="card-text mb-3 petition-full--sign-input">
                <input type="hidden" value="" id="input-signature-id" hidden>
                <input type="hidden" value="'.$petition_code.'" id="input-code" hidden>
                <div class="input-group flex-nowrap mb-2">
                    <label class="input-group-text" for="input-name">'.self::__('name').'</label>
                    <input type="text" class="form-control" placeholder="'.self::__('name').'" aria-label="'.self::__('name').'" id="input-name">
                </div>
                <div class="input-group flex-nowrap mb-2">
                    <label class="input-group-text" for="input-email">'.self::__('email').'</label>
                    <input type="email" class="form-control" placeholder="'.self::__('email').'" aria-label="'.self::__('email').'" id="input-email">
                </div>
                <div class="input-group flex-nowrap mb-2">
                    <label class="input-group-text" for="input-country">'.self::__('country').'</label>
                    <select class="form-select" id="input-country">
                        <option selected disabled>'.$lang->choose.'...</option>';
                        Country::SelectAllCountriesWithIndex(null);
                        echo '
                    </select>
                </div>
            </div>
            <div class="card-text mb-2">
                <button id="btn_sign" class="btn btn-lg btn-danger d-block w-100"><i class="fas fa-file-signature"></i> '.self::__('sign').'</button>
            </div>
            <small class="card-text d-block text-muted">
                <i class="fas fa-exclamation"></i> '.self::__('disclaimer').'
            </small>
        </div>';
        echo "<script>
        var __get_by_id = (id) => {
            return document.getElementById(id);
        };
        var get_input_value = (tag) => {
            return __get_by_id('input-'+tag).value;
        };
        __get_by_id('btn_sign').onclick = (ev) => {
            ev.preventDefault();
            var code = get_input_value('code'),
            name = get_input_value('name'),
            email = get_input_value('email'),
            country = get_input_value('country');
            API('sign-petition', 'post', {
                'code': code,
                'name': name,
                'email': email,
                'country': country
            }, () => {
                $('#full-sign-petition').hide('fast');
                $('#full-sign-petition--result').html(spinnerLoading);
                $('#full-sign-petition--result').show('slow');
                $('#sign-verification-code-area').hide('fast');
            }, (response) => {
                console.log(response);
                $('#full-sign-petition').hide('fast');
                if(response.success === true) {
                    ShowAlertResponse('#full-sign-petition--result', true, '".self::__('sign_done')."', response.message);
                    $('#input-signature-id').val(response.data.id);
                    $('#sign-verification-code-area').show('slow');
                } else if(Number(response.success) === -1) {
                    ShowAlertResponse('#full-sign-petition--result', -1, '', response.message);
                    appendSignedPetition($('#input-code').val());
                } else if(Number(response.success) === -2) {
                    ShowAlertResponse('#full-sign-petition--result', -1, '', response.message);
                    appendMyPetition($('#input-code').val());
                } else {
                    ShowAlertResponse('#full-sign-petition--result', false, '".self::__('sign_fail')."', response.message);
                    $('#full-sign-petition').show('slow');
                }
            });
        };
        </script>";
    }
    public static function ShowSingleSmall($petition) {
        $signatures = self::GetVerifiedSignatures($petition['id']);
        $signatures_number = count($signatures);
        $img = Image::Read($petition['img']);
        if($img) $img = $img['data'];
        $creator = self::GetCreator($petition['creator']);
        $header_coloring_class = 'bg-light text-dark';
        if(!$petition['verified']) {
            $header_coloring_class = 'bg-warning text-dark';
        } else {
            if(intval($petition['status']) === 1) {
                $header_coloring_class = 'bg-success text-light';
            } elseif(intval($petition['status']) === -1) {
                $header_coloring_class = 'bg-danger text-light';
            }
        }
        echo '
        <div class="petition-small col">
            <div class="card h-100">
                <img src="'.($img ? $img : path_join('/public/img/default_petition.png')).'" class="card-img-top">
                <div class="card-header card-img-top '.$header_coloring_class.' col-12 text-center p-3">
                    <h5 class="card-title">'.$petition['title'].'</h5>
                    <p class="card-text"><small class="'.(intval($petition['status']) !== 0 ? 'text-light' : 'text-muted').'"><i class="far fa-user"></i> '.self::_('by').': <i class="flag flag-'.convertCountryNameToFontIconName($creator['country']).'" title="'.$creator['country'].'"></i>'.$creator['name'].'</small></p>
                </div>
                <div class="card-body">
                    <p class="card-text">'.mb_substr($petition['body'], 0, 128).'...</p>
                </div>
                <div class="card-footer">
                    <div class="card-text mb-3">
                        <div class="row">
                            <small class="col text-muted"><i class="fas fa-tags"></i> '.self::_('tag').': '.self::GetTags($petition['tags'])[0].'</small>
                            <small class="col text-muted"><i class="fas fa-user-friends"></i> '.number_format($signatures_number).' '.self::_('signatures').'</small>
                        </div>
                    </div>
                    <p class="card-text">
                        <button'.js_onclick_load_page_args('petition', ['code' => $petition['code']]).'class="btn btn-outline-primary d-block w-100">'.self::_('view').'</button>
                    </p>
                </div>
            </div>
        </div>';
    }
    public static function ShowSingleFull($petition) {
        if(!$petition) {
            echo showResponseAlert('warning', self::_('petition_does_not_exist'), 'h5');
            return;
        }
        $lang = load_requested_lang();
        $signatures = self::GetVerifiedSignatures($petition['id']);
        $signatures_number = count($signatures);
        $img = Image::Read($petition['img']);
        if($img) $img = $img['data'];
        $creator = self::GetCreator($petition['creator']);
        $how_many_last_signers = 3;
        echo '<input type="hidden" value="'.$petition['code'].'" id="petition-code" hidden>
        <div class="petition-full card border-light rounded-start col-12">';
        if(!$petition['verified']) {
            echo '<div class="text-center">
                <div class="alert alert-warning p-4">
                    <h3 class="mb-3"><i class="fas fa-exclamation-triangle"></i> '.self::_('waiting_verify').'</h3>
                    <p><i class="fas fa-envelope-open-text"></i> '.self::_('check_email_to_verify_petition').'</p>
                    <div class="petition-full--verification-box text-center" id="verification-box">
                        <div class="input-group flex-nowrap mb-2">
                            <label class="input-group-text" for="input-verification-code">'.self::___('verification_code').'</label>
                            <input type="text" id="input-verification-code" placeholder="'.self::___('verification_code').'" class="form-control" style="direction:ltr;">
                        </div>
                        <div class="flex-nowrap text-center">
                            <div id="verification-box-processing"></div>
                            <button id="verification-btn" class="btn btn-lg btn-primary"><i class="far fa-check-circle"></i> '.$lang->verify.'</button>
                        </div>
                    </div>
                </div>
            </div>';
            echo "<script>
            $('#verification-box-processing').html(spinnerLoading);
            $('#verification-box-processing').hide('fast');
            $('#verification-btn').click((ev) => {
                ev.preventDefault();
                API('verify-petition', 'patch', {
                    'petition_code': $('#petition-code').val(),
                    'verification_code': $('#input-verification-code').val()
                }, () => {
                    $('#verification-box-processing').show('slow');
                    $('#verification-btn').hide('slow');
                }, (response) => {
                    console.log(response);
                    if(response.success) {
                        ShowAlertResponse('#verification-box-processing', true, '".self::___('verification_process_done')."', response.message);
                    } else {
                        ShowAlertResponse('#verification-box-processing', false, '".self::___('verification_process_failed')."', response.message);
                        $('#verification-btn').show('slow');
                    }
                });
            });
            </script>";
        }
        $header_coloring_class = 'bg-light text-dark';
        if(!$petition['verified']) {
            $header_coloring_class = 'bg-warning text-dark';
        } else {
            if(intval($petition['status']) === 1) {
                $header_coloring_class = 'bg-success text-light';
            } elseif(intval($petition['status']) === -1) {
                $header_coloring_class = 'bg-danger text-light';
            }
        }
        echo '<div class="row g-0">
            <div class="col-12 card-header '.$header_coloring_class.' text-center p-3">
                <h3 class="card-title mb-0">'.$petition['title'].'</h3>
            </div>';
            echo '<div class="col-8 container">
                <div class="card-body">
                    <div class="col-12 mb-4">
                        <img src="'.($img ? $img : path_join('/public/img/default_petition.png')).'" class="img-fluid rounded-start">
                    </div>
                    <div class="col-12">
                        <div class="card-text petition-full--text">'.nl2br($petition['body']).'</div>
                        <hr>
                        <div class="card-text petition-full--footer">
                            <div class="row">
                                <small class="col text-muted"><i class="far fa-user"></i> '.self::_('by').': <i class="flag flag-'.convertCountryNameToFontIconName($creator['country']).'" title="'.$creator['country'].'"></i>'.$creator['name'].'</small>
                                <small class="col text-muted"><i class="fas fa-tags"></i> '.self::_('tags').': '.implode(", ", self::GetTags($petition['tags'])).'</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 container">
                <div class="card-body">
                    <div class="col-12 mb-3">
                        <div class="card-text">';
                        if(count($signatures) > 0) {
                            echo '<p class="petition-full--hsigned"><b><i class="fas fa-users"></i> '.number_format($signatures_number).' '.self::_('n_signed').'</b></p>';
                        } else {
                            echo '<p class="petition-full--hsigned--no-one"><b><i class="fas fa-user"></i> '.self::_('no_one_signed_note').'</b></p>';
                        }
                        echo '</div>
                    </div>';
                    if(count($signatures) > 0) {
                        echo '<hr><div class="col-12 mb-3">
                            <div class="card-text petition-full--signs">';
                                echo '<ul class="list-unstyled text-muted">
                                ';
                                foreach(array_reverse($signatures) as $i => $signature) {
                                    if($i+1 > $how_many_last_signers) break;
                                    echo "<li><small><i class='fas fa-user-edit'></i> <i class='flag flag-".convertCountryNameToFontIconName($signature['signee']['country'])."' title='".$signature['signee']['country']."'></i><b>".$signature['signee']['name']."</b> ".self::_('signed_petition').".</small></li>";
                                }
                                echo '
                                </ul>
                            </div>
                        </div>';
                    }
                    echo '<hr>
                    <div class="col-12 mb-3">';
                    echo '<div id="the-full-sign-section">';
                    if($petition['verified']) {
                        if(intval($petition['status']) === 1) {
                            echo showResponseAlert('success', self::_('petition_status_victory_end'), 'h5');
                        } elseif(intval($petition['status']) === -1) {
                            echo showResponseAlert('danger', self::_('petition_status_fail_end'), 'h5');
                        } else {
                            self::showSignForm($petition['code']);
                        }
                    }
                    echo '</div>';
                    echo '</div>
                    </div>
                </div>
            </div>
        </div>';
        echo "<script>
        if(isSignedPetition($('#petition-code').val())) {
            $('#the-full-sign-section').html(ShowAlertResponse('#the-full-sign-section', true, '', '".self::__('sign_done')."'));
        }
        if(isMyPetition($('#petition-code').val())) {
            $('#the-full-sign-section').html(`<div class='alert alert-secondary'><div class='mb-0'><i class='far fa-thumbs-up'></i> ".self::_('your_petition').".</div></div>`);
        }
        </script>";
    }
    public static function SearchResults($query) {
        $query = trim(sanitize_string($query));
        if(strlen($query) < 3) return [];
        $stmt = DB::ExecuteQuery("SELECT * FROM ".self::DB_TABLE." WHERE title LIKE ?", "%$query%", PDO::PARAM_STR);
        if($stmt !== false) return $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [];
    }
}
?>
