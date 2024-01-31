@props([
    'user' => 'landlord',
    'selector' => '',
    'table' => null,
    'column' => 'slug',
    'beforeMsg' => true,
    'disableBtn' => false,
    'disableBtnSelector' => ''
])

@php
    $userType = $user ?? 'tenant';
@endphp

<script>
    $(document).ready(() => {
        let timer = null;
        const beforeMsg = `{{$beforeMsg}}`;
        const selector = `{{$selector}}`;
        const disableBtn = `{{$disableBtn}}`;
        const disableBtnSelector = `{{$disableBtnSelector}}`;

        $(document).on('keyup', selector, function () {
            let value = $(this).val();

            if(value.length === 0)
            {
                $('.unique-response-text').remove();
                return "";
            }
            beforeValidator();

            clearTimeout(timer);

            timer = setTimeout(() => {
                sendRequest(value);
            }, 800);
        })

        function sendRequest(value)
        {
            const table = `{{$table}}`;
            const column = `{{$column}}`;

            $.ajax({
                type: 'POST',
                url: `{{route("{$userType}.unique-checker")}}`,
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}",
                },
                data: {
                    table: table,
                    column: column,
                    value: value
                },
                success: function (response){
                    $('.unique-response-text').remove();
                    $(selector).after(searchMarkup(response.type, response.msg));

                    if (disableBtn && response.type === 'success')
                    {
                        $(disableBtnSelector).attr('disabled', false);
                    }
                },
                error: function (response){

                }
            });
        }

        let searchMarkup = (type, msg) => {
            return `<p class="unique-response-text text-${type}">${msg}</p>`;
        }

        let beforeValidator = () => {
            if(beforeMsg)
            {
                $('.unique-response-text').remove();
                $(selector).after(searchMarkup('info', `Checking if the {{$column}} is unique..`));
            }

            if (disableBtn)
            {
                $(disableBtnSelector).attr('disabled', true);
            }
        }
    });
</script>
