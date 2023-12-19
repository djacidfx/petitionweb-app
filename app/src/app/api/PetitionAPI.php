<?php
class PetitionAPI extends API {
    public function Create() {
        $body = $this->body;
        $lang = $this->lang;
        if(
            strlen($body['petition_title']) > 12 && strlen($body['petition_body']) > 12 &&
            $body['petition_img'] && count($body['petition_tags']) > 0 &&
            $body['creator_name'] && $body['creator_email'] && intval($body['creator_country']) >= 0
        ) {
            if(filter_var($body['creator_email'], FILTER_VALIDATE_EMAIL)) {
                if(in_array(Country::$country_array[$body['creator_country']], Country::$country_array)) {
                    if(check_base64_image($body['petition_img'], extract_image_type_from_base64($body['petition_img']))) {
                        if(count($body['petition_tags']) <= MAXIMUM_TAGS_CAN_CHOOSE) {
                            if(items_in_array($body['petition_tags'], ALL_PETITION_TAGS)) {
                                $op = Petition::CreateDriver(
                                    $body['petition_title'], $body['petition_body'], $body['petition_img'], $body['petition_tags'],
                                    $body['creator_name'], $body['creator_email'], $body['creator_country']
                                );
                                if($op[0] === true) {
                                    $PetitionObject = Petition::Read('code', $op[1]);
                                    $verification_code = Petition::GenerateVerificationCode($PetitionObject);
                                    file_put_contents(PHP_BASE_PATH.'/temp/.verification-codes-petition', '['.$op[1].'] '.$verification_code.PHP_EOL, FILE_APPEND);
                                    $email_op = @sendEmail(WEB_MASTER_EMAIL, $body['creator_email'], $lang->petition_form->verification_email_subject, $lang->petition_form->verification_email_msg.":<br>\r\n".$verification_code);
                                    if($email_op[0]) {
                                        return $this->Response(true, 201, $lang->petition_view->check_email_to_verify_petition, ['code'=>$op[1]]);
                                    } else {
                                        return $this->Response(true, 201, $lang->petition_form->petition_created_but_email_not_sent, ['code'=>$op[1]]);
                                    }
                                } else {
                                    return $this->Response(false, 500, $lang->db_false_err, []);
                                }
                            } else {
                                return $this->Response(false, 400, $lang->petition_form->tags_invalid, []);
                            }
                        } else {
                            return $this->Response(false, 400, $lang->petition_form->only_limited_tags." ".MAXIMUM_TAGS_CAN_CHOOSE, []);
                        }
                    } else {
                        return $this->Response(false, 400, $lang->petition_form->img_data_invalid, []);
                    }
                } else {
                    return $this->Response(false, 400, $lang->petition_form->country_invalid, []);
                }
            } else {
                return $this->Response(false, 400, $lang->petition_form->email_invalid, []);
            }
        } else {
            return $this->Response(false, 400, $lang->petition_form->empty_fields_msg, []);
        }
    }
    public function Verify() {
        $body = $this->body;
        $lang = $this->lang;
        if($body['petition_code'] && $body['verification_code']) {
            $Petition = Petition::Read('code', $body['petition_code']);
            if($Petition) {
                if(Petition::ValidateVerificationCode($Petition, $body['verification_code'])) {
                    if(Petition::VerifyPetition($Petition['id'])) {
                        return $this->Response(true, 200, '', []);
                    } else {
                        return $this->Response(false, 500, $lang->db_false_err, []);
                    }
                } else {
                    return $this->Response(false, 400, $lang->petition_form->invalid_verification_code, []);
                }
            } else {
                return $this->Response(false, 404, $lang->petition_view->petition_does_not_exist, []);
            }
        } else {
            return $this->Response(false, 400, $lang->form_empty_fields, []);
        }
    }
    public function Sign() {
        $body = $this->body;
        $lang = $this->lang;
        if($body['code'] && $body['name'] && $body['email'] && intval($body['country']) >= 0) {
            if(filter_var($body['email'], FILTER_VALIDATE_EMAIL)) {
                if(in_array(Country::$country_array[$body['country']], Country::$country_array)) {
                    $Petition = Petition::Read('code', $body['code']);
                    $petition_id = $Petition['id'];
                    if(!Petition::IsSigneeSigned($petition_id, $body['email'])) {
                        $signee = Petition::SetupCreator($body['name'], $body['email'], Country::$country_array[$body['country']]);
                        if(Petition::GetCreator($Petition['creator']) !== $body['email']) {
                            $op = Signatures::Create($petition_id, $signee);
                            if($op[0] === true) {
                                $SignatureObject = Signatures::Read('id', $op[1]);
                                $verification_code = Petition::GenerateVerificationCode($SignatureObject);
                                file_put_contents(PHP_BASE_PATH.'/temp/.verification-codes-signature', '['.$op[1].'] '.$verification_code.PHP_EOL, FILE_APPEND);
                                $email_op = @sendEmail(WEB_MASTER_EMAIL, $body['email'], $lang->sign_form->verification_email_subject, $lang->sign_form->verification_email_msg.":<br>\r\n".$verification_code);
                                if($email_op[0]) {
                                    return $this->Response(true, 201, $lang->sign_form->check_email_to_verify, ['id'=>$op[1]]);
                                } else {
                                    return $this->Response(true, 201, $lang->sign_form->created_but_email_not_sent, ['id'=>$op[1]]);
                                }
                            } else {
                                return $this->Response(false, 500, $lang->db_false_err, []);
                            }
                        } else {
                            return $this->Response(-2, 200, $lang->sign_form->your_petition, []);
                        }
                    } else {
                        return $this->Response(-1, 200, $lang->sign_form->signed_before, []);
                    }
                } else {
                    return $this->Response(false, 400, $lang->petition_form->country_invalid, []);
                }
            } else {
                return $this->Response(false, 400, $lang->sign_form->email_invalid, []);
            }
        } else {
            return $this->Response(false, 400, $lang->form_empty_fields, []);
        }
    }
    public function VerifySignature() {
        $body = $this->body;
        $lang = $this->lang;
        if($body['verification_code'] && $body['signature_id']) {
            $SignatureObject = Signatures::Read('id', $body['signature_id']);
            if($SignatureObject) {
                if(Petition::ValidateVerificationCode($SignatureObject, $body['verification_code'])) {
                    if(Signatures::VerifySignature($SignatureObject['id'])) {
                        return $this->Response(true, 200, '', []);
                    } else {
                        return $this->Response(false, 500, $lang->db_false_err, []);
                    }
                } else {
                    return $this->Response(false, 400, $lang->petition_form->invalid_verification_code, []);
                }
            } else {
                return $this->Response(false, 404, '', []);
            }
        } else {
            return $this->Response(false, 400, $lang->form_empty_fields, []);
        }
    }
}
?>