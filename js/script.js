jQuery(function($){
    if ($('.tzportfolio-form-builder').length) {
        $(document).on('submit', '.tzportfolio-form-builder' , function (e) {
            e.preventDefault();
            var request = {},
                $this   = $(this),
                data    = $this.serializeArray();

            for (let i = 0; i < data.length; i++) {
                request[data[i]['name']] = data[i]['value'];
            }
            request[$this.find('.token').attr('name')] = 1;
            $.ajax({
                type   : 'POST',
                data   : request,
                beforeSend: function(){
                    $this.find('.tzportfolio-formbuilder-status').empty()
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $this.find('.tzportfolio-formbuilder-status').append('<div class="uk-alert-success" uk-alert><a class="uk-alert-close" uk-close></a><p>'+response.message+'</p></div>');
                        $this.trigger("reset");
                    } else {
                        $this.find('.tzportfolio-formbuilder-status').append('<div class="uk-alert-danger" uk-alert><a class="uk-alert-close" uk-close></a><p>'+JSON.stringify(response)+'</p></div>');
                    }
                }
            });
        });
    }
});