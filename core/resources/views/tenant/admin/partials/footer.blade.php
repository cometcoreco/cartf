<footer class="footer">
    <div class="container-fluid d-flex justify-content-between">
        <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">
            {!! get_footer_copyright_text() !!}
        </span>
        <span class="float-none float-sm-end mt-1 mt-sm-0 text-end"> v- <strong>{{get_static_option_central('get_script_version')}}</strong></span>
    </div>
</footer>
</div>
</div>
</div>

<script src="{{global_asset('assets/landlord/admin/js/vendor.bundle.base.js')}}"></script>
<script src="{{global_asset('assets/landlord/admin/js/hoverable-collapse.js')}}"></script>
<script src="{{global_asset('assets/landlord/admin/js/off-canvas.js')}}"></script>
<script src="{{global_asset('assets/landlord/admin/js/misc.js')}}"></script>
<script src="{{global_asset('assets/landlord/common/js/axios.min.js')}}"></script>
<script src="{{global_asset('assets/landlord/common/js/sweetalert2.js')}}"></script>
<script src="{{global_asset('assets/common/js/countdown.jquery.js')}}"></script>
<script src="{{global_asset('assets/common/js/flatpickr.js')}}"></script>
<x-flatpicker.flatpickr-locale/>
<script src="{{global_asset('assets/common/js/toastr.min.js')}}"></script>
<script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>

<!-- Javascript Helpers -->
<script src="{{ global_asset('assets/js/helpers.js') }}"></script>

<script>
    window.appUrl = "{{ url('/') }}";
    window.currencySymbol = {
        currencyPosition: `{{get_static_option('site_currency_symbol_position')}}`,
        symbol: `{{site_currency_symbol()}}`
    };

    toastr.options = {
        "closeButton": true,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "slideUp",
        "rtl": "{{get_user_lang_direction() == 1}}"
    }

    const translatedDataTable = () => {
        return {
            "decimal": "",
            "emptyTable": "{{__('No data available in table')}}",
            "info": "{{__('Showing')}} _START_ {{__('to')}} _END_ {{__('of')}} _TOTAL_ {{__('entries')}}",
            "infoEmpty": "{{__('Showing')}} 0 {{__('to')}} 0 {{__('of')}} 0 {{__('entries')}}",
            "infoFiltered": "({{__('filtered from')}} _MAX_ {{__('total entries')}})",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "{{__('Show')}} _MENU_ {{__('entries')}}",
            "loadingRecords": "{{__('Loading...')}}",
            "processing": "",
            "search": "{{__('Search:')}}",
            "zeroRecords": "{{__('No matching records found')}}",
            "paginate": {
                "first": "{{__('First')}}",
                "last": "{{__('Last')}}",
                "next": "{{__('Next')}}",
                "previous": "{{__('Previous')}}"
            },
            "aria": {
                "sortAscending": ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    }

    (function($){
        "use strict";

        $(document).ready(function ($) {
            sidebar();

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            $('select.select2').select2();
            $(document).on('click','.swal_delete_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure?")}}',
                    text: '{{__("You would not be able to revert this item!")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('Yes, delete it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_change_language_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to make this language as a default language?")}}',
                    text: '{{__("Languages will be turn changed as default")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('Yes, Change it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_change_approve_payment_button',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to approve this payment?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('Yes, Accept it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            $(document).on('click','.swal_status_change',function(e){
                e.preventDefault();
                Swal.fire({
                    title: '{{__("Are you sure to change this status?")}}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{__('Yes, Change it!')}}",
                    cancelButtonText: "{{__('Cancel')}}",
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).next().find('.swal_form_submit_btn').trigger('click');
                    }
                });
            });

            let light = false;
            $(document).on('click', '.tenant_info_icon', function(){
                $('.tenant_info_list').slideToggle(300);
                if(light === false){
                    $('.tenant_info_icon i').removeClass('mdi-lightbulb-on-outline');
                    $('.tenant_info_icon i').addClass('mdi-lightbulb-on');
                    $('.tenant_info_list').addClass('open-info');
                    light = true;
                } else {
                    $('.tenant_info_icon i').addClass('mdi-lightbulb-on-outline');
                    $('.tenant_info_icon i').removeClass('mdi-lightbulb-on');
                    $('.tenant_info_list').removeClass('open-info');
                    light = false;
                }
            });

            $(document).on('click', '.submenu-disabled', function (e){
                e.preventDefault();

                let text = $(this).text();

                Swal.fire({
                    title: '{{__("Coming Soon")}}'+'\n'+text,
                    text: '{{__("This feature is currently under development and will be available soon. We are committed to continuously enhancing our application to provide you with the best possible user experience.")}}',
                    icon: 'info',
                    showCancelButton: false,
                    confirmButtonColor: '#3fc3ee',
                    confirmButtonText: "{{__('Got it')}}",
                });
            });
        });
    })(jQuery);

    window.addEventListener('click', function(e){
        if (!document.getElementById('tenant_info_list').contains(e.target)){
            if($('.open-info').length == 1)
            {
                $('.tenant_info_icon').trigger('click');
            }
        }
    });

    function sidebar()
    {
        let tax_system = `{{get_static_option('tax_system') ?? 'zone_wise_tax_system'}}`;
        let tax_manage_menu = $('#tax-manage-menu-items');
        let country_state = tax_manage_menu.find('ul').children().slice(0,2);
        let tax_class = tax_manage_menu.find('ul').children().slice(3,4);
        if(tax_system === 'zone_wise_tax_system'){
            country_state.fadeIn();
            tax_class.fadeOut();
        } else {
            country_state.fadeOut();
            tax_class.fadeIn();
        }
    }
</script>

<script>
    $(".date").flatpickr({
        enableTime: true,
        minDate: "today",
        time_12hr: true,
        altInput: true,
        defaultDate: "2018-04-24 16:57"
    });
</script>

<script>
    $(document).ready(() => {
        let timeout = null;
        $(document).on('keyup', '.global-search-input', function (e) {
            e.preventDefault();

            let search = $(this).val();

            beforeSearch();

            clearTimeout(timeout);
            timeout = setTimeout(function() {
                sendSearchRequest(search);
            }, 350);
        });

        let sendSearchRequest = (search_ext) => {
            let search = search_ext;
            let search_dropdown = $('.search-dropdown');

            $.ajax({
                type: 'GET',
                url: `{{route('tenant.admin.search.global')}}`,
                data: {
                    query: search
                },
                success: function (data) {
                    search_dropdown.empty();

                    let item = '';

                    if (data.response.length === 0)
                    {
                        item = searchMarkup('#', 'no result found');
                        search_dropdown.append(item);
                        search_dropdown.addClass('show');
                        search_dropdown.removeClass('loader-item');
                        return;
                    }

                    if (search === '')
                    {
                        search_dropdown.removeClass('show');
                        search_dropdown.removeClass('loader-item');
                        return;
                    }

                    $.each(data.response, function (key, value) {
                        item = searchMarkup(key, value);
                        search_dropdown.append(item);
                    });

                    search_dropdown.addClass('show');
                    search_dropdown.removeClass('loader-item');
                },
                error: function (data)
                {
                    search_dropdown.removeClass('show');
                }
            });
        }

        let searchMarkup = (key, value) => {
            return `<a class="search-item dropdown-item preview-item loader-item" href="${key}">
                             <div class="search-text-wrapper preview-item-content d-flex align-items-start flex-column justify-content-center">
                                   <h6 class="search-text preview-subject mb-1 font-weight-normal text-capitalize">${value}</h6>
                             </div>
                       <div class="dropdown-divider"></div>
                       </a>`;
        }

        let beforeSearch = () => {
            let search_dropdown = $('.search-dropdown').addClass('show');
            search_dropdown.empty();

            let item = searchMarkup('#', `<i class="mdi mdi-spin mdi-loading"></i>`);
            search_dropdown.append(item);
        };
    });
</script>

@yield('scripts')

<!-- footer for PWA -->
@yield('pwa-footer')
</body>
</html>
