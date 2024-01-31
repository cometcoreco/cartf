<!-- footer area start -->
<footer class="footer-area body-bg-2 theme-aromatic-footer">
    <div class="container-three">
        <div class="footer-middle padding-top-30 padding-bottom-60">
            <div class="row align-items-center">
                {!! render_frontend_sidebar('footer',['column' => true]) !!}
            </div>
        </div>
        <div class="copyright-area copyright-border">
            <div class="row align-items-center">
                {!! render_frontend_sidebar('footer_bottom_left',['column' => true]) !!}

                <div class="col-lg-4 col-md-6">
                    <div class="copyright-contents">
                        {!! get_footer_copyright_text() !!}
                    </div>
                </div>

                {!! render_frontend_sidebar('footer_bottom_right',['column' => true]) !!}
            </div>
        </div>
    </div>
</footer>
<!-- footer area end -->
