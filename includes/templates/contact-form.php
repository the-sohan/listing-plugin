<div id="form_success" style="background-color: green; color: #fff" ></div>
<div id="form_error" style="background-color: red; color: #fff" ></div>

<form id="enquery_form">

    <?php wp_nonce_field('wp_rest'); ?>

    <label for="name">Post Title</label><br>
    <input type="text" name="name" id=""><br>
    
    <label for="email">Your Email</label><br>
    <input type="email" name="email" id=""><br>

    <label for="number">Your Number</label><br>
    <input type="number" name="number" id=""><br>

    <label for="message">Feature List</label><br>
    <textarea name="message" id=""></textarea><br><br>

    <button type="submit">Submit</button>

</form>

<script>
    
    jQuery(document).ready(function($) {

        $("#enquery_form").submit( function(event) {

            event.preventDefault();

            var form = $(this);

            //console.log(form.serialize());

            // alert('ok');

            $.ajax({
                type: "POST",
                url: "<?php echo get_rest_url( null, 'v1/contact-form/submit' ); ?>",
                data: form.serialize(),
                success: function(res){
                    form.hide();
                    $( "#form_success" ).html(res).fadeIn();
                },
                error: function(){
                    $( "#form_error" ).html( "There was an error, message not send" ).fadeIn();
                }

            });

        });

    });

</script>

