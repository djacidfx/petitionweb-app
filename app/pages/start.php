<?php
$form_lang = $lang->petition_form;
?>
<div id="start-petition-page">
    <iframe onload="onThisPageLoaded()" frameborder="0" style="display:none;"></iframe>
    <div class="card border-light rounded-start col-12" id="start-petition-form">
        <div class="row g-0">
            <div class="col-12 card-header text-center p-3 bg-dark text-light"><h3><?php echo $lang->start_petition_page_head;?></h3></div>
            <div class="col-12 container">
                <div id="s1-section" class="s-section">
                    <div class="card-header bg-light text-dark flex-nowrap mb-2"><h4><?php echo $form_lang->enter_your_info;?></h4><p class="mb-0"><?php echo $form_lang->user_info_head_note;?></p></div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-name"><?php echo $form_lang->your_name;?></label>
                        <input type="text" id="input-name" placeholder="<?php echo $form_lang->your_name;?>" class="form-control">
                    </div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-email"><?php echo $form_lang->your_email;?></label>
                        <input type="email" id="input-email" placeholder="<?php echo $form_lang->your_email;?>" class="form-control">
                    </div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-country"><?php echo $form_lang->your_country;?></label>
                        <select id="input-country" class="form-select">
                            <option value="" selected disabled><?php echo $lang->choose;?>...</option>
                            <?php Country::SelectAllCountriesWithIndex(null); ?>
                        </select>
                    </div>
                    <div class="input-group flex-nowrap mb-2 row g-0">
                        <button onclick="show_section(2)" id="next-1" class="btn btn-lg btn-primary d-block col-12" disabled="disabled"><?php echo $lang->next;?> &gt; </button>
                    </div>
                </div>

                <div id="s2-section" class="s-section" style="display: none;">
                    <div class="card-header bg-light text-dark flex-nowrap mb-2"><h4><?php echo $form_lang->enter_petition_details;?></h4></div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-title"><?php echo $form_lang->title;?></label>
                        <input type="text" id="input-title" placeholder="<?php echo $form_lang->title;?>" class="form-control">
                    </div>
                    <div class="input-group flex-nowrap mb-2 g-0">
                        <label class="input-group-text" for="input-tags"><?php echo $form_lang->tags;?></label>
                        <select id="input-tags" class="form-select" multiple aria-label="multiple select example">
                            <option value="" selected disabled><?php echo $lang->choose;?>...</option>
                            <?php
                            foreach($petition_total_tags as $tag) {
                                echo "<option value='$tag'>$tag</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-group flex-nowrap mb-2 row g-0">
                        <button onclick="show_section(1)" class="btn btn-lg btn-outline-secondary d-block col-4"> &lt; <?php echo $lang->back;?></button>
                        <button onclick="show_section(3)" id="next-2" class="btn btn-lg btn-primary d-block col-8" disabled="disabled"><?php echo $lang->next;?> &gt; </button>
                    </div>
                </div>

                <div id="s3-section" class="s-section" style="display: none;">
                    <div class="card-header bg-light text-dark flex-nowrap mb-2"><h4><?php echo $form_lang->enter_petition_details;?></h4></div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-img"><?php echo $form_lang->img;?></label>
                        <input type="file" id="input-img" placeholder="<?php echo $form_lang->img;?>" class="form-control">
                    </div>
                    <div class="input-group flex-nowrap mb-2">
                        <label class="input-group-text" for="input-body"><?php echo $form_lang->body;?></label>
                        <textarea id="input-body" placeholder="<?php echo $form_lang->body;?>" style="height: 300px;" class="form-control"></textarea>
                    </div>
                    <div class="input-group flex-nowrap mb-2 row g-0">
                        <button onclick="show_section(2)" class="btn btn-lg btn-outline-secondary d-block col-4"> &lt; <?php echo $lang->back;?></button>
                        <button id="next-3" class="btn btn-lg btn-primary d-block col-8" disabled="disabled"><?php echo $lang->start;?> &gt; </button>
                    </div>
                </div>

                <div id="s4-section" class="s-section" style="display: none;">
                    <div id="processing-content" class="my-3 text-center">
                        <div id="processing-spinner" class="mb-4"></div>
                        <h4 class="mb-0"><?php echo $lang->on_data_processing;?></h4>
                    </div>
                    <hr>
                    <div id="result-content" class="text-center my-2" style="display: none;"></div>
                    <div id="back-content" style="display: none;">
                        <button onclick="show_section(3)" class="btn btn-lg btn-outline-secondary d-block col-12"> &lt; <?php echo $lang->back;?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var __get_by_id = (id) => {
    return document.getElementById(id);
};
var show_section = (number) => {
    var sections = document.getElementsByClassName('s-section');
    for(var i = 0; i < sections.length; i++) {
        __get_by_id('s'+(i+1)+'-section').style.display = 'none';
    }
    __get_by_id('s'+number+'-section').style.display = 'block';
};
var get_input_value = (tag) => {
    return __get_by_id('input-'+tag).value;
};
var get_file_input = (id) => {
    return __get_by_id('input-img').files[0];
};
var check_btn_state = () => {
    if(get_input_value('name') && get_input_value('email') && get_input_value('country')) {
        __get_by_id('next-1').removeAttribute('disabled');
    } else {
        __get_by_id('next-1').setAttribute('disabled', 'disabled');
    }
    if(get_input_value('title') && get_input_value('tags')) {
        __get_by_id('next-2').removeAttribute('disabled');
    } else {
        __get_by_id('next-2').setAttribute('disabled', 'disabled');
    }
    if(get_input_value('img') && get_input_value('body')) {
        __get_by_id('next-3').removeAttribute('disabled');
    } else {
        __get_by_id('next-3').setAttribute('disabled', 'disabled');
    }
};
var all_inputs = document.querySelectorAll('#start-petition-form input, #start-petition-form select, #start-petition-form textarea');
for(var i = 0; i < all_inputs.length; i++) {
    all_inputs[i].oninput = (ev) => {
        check_btn_state();
    };
}
__get_by_id('processing-spinner').innerHTML = spinnerLoading;

function onThisPageLoaded() {
    __get_by_id('input-img').onchange = (ev) => {
        var file = get_file_input('input-img');
        if(file) {
            if(!(IMAGE_MIME_REGEX.test(file.type))) {
                Modal(null, "<?php echo $lang->petition_form->image_type_not_allowed;?>", null, null);
            }
        }
    };
    __get_by_id('input-tags').onchange = (ev) => {
        var __tags = getMultipleSelect(__get_by_id('input-tags'));
        if(__tags.length > <?php echo $maximum_tags_can_choose;?>) {
            Modal(null, "<?php echo $lang->petition_form->only_limited_tags;?> <?php echo $maximum_tags_can_choose;?>", null, null);
        }
    };
    __get_by_id('next-3').onclick = (ev) => {
        ev.preventDefault();
        show_section(4);
        var name = get_input_value('name'),
        email = get_input_value('email'),
        country = get_input_value('country'),
        title = get_input_value('title'),
        tags = getMultipleSelect(__get_by_id('input-tags')),
        img = get_input_value('img'),
        body = get_input_value('body');

        if(name && email && country && title && tags && img && body) {
            if(tags.length <= <?php echo $maximum_tags_can_choose;?>) {
                var file = get_file_input('input-img');
                loadFile(file, (image_data) => {
                    if(IMAGE_MIME_REGEX.test(file.type)) {
                        API('start-petition', 'post', {
                            'creator_name': name,
                            'creator_email': email,
                            'creator_country': country,
                            'petition_title': title,
                            'petition_tags': tags,
                            'petition_img': image_data,
                            'petition_body': body
                        }, () => {
                            $('#processing-content').show('slow');
                            $('#result-content').hide('fast');
                            $('#back-content').hide('fast');
                        }, (response) => {
                            console.log(response);
                            $('#processing-content').hide('slow');
                            $('#back-content').hide('slow');
                            if(response.success && response.data.code) {
                                ShowAlertResponse('#result-content', true, '<?php echo $form_lang->create_petition_success;?>', `<p>${response.message}</p><button class="btn btn-lg btn-primary" onclick="load_page_content_with_args('petition', {'code': '${response.data.code}'})"><?php echo $form_lang->go_to_your_petition;?></button>`);
                                appendMyPetition(response.data.code);
                            } else {
                                ShowAlertResponse('#result-content', false, '<?php echo $form_lang->create_petition_fail_head;?>', response.message);
                                $('#back-content').show('slow');
                            }
                            $('#result-content').show('slow');
                        });
                    } else {
                        Modal(null, "<?php echo $lang->petition_form->image_type_not_allowed;?>", null, null);
                        show_section(3);
                    }
                });
            } else {
                Modal(null, "<?php echo $lang->petition_form->only_limited_tags;?> <?php echo $maximum_tags_can_choose;?>", null, null);
                show_section(3);
            }
        }
    };
};
</script>