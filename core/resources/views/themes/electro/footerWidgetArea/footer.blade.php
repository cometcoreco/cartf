<footer class="footer-area bg-item-four position-relative theme-electro-footer">
    <div class="section-top-shape">
        <img src="assets/img/section-shapes/section-top-s.png" alt="">
    </div>
    <div class="container-three">
        <div class="footer-top-contents footer-top-border padding-top-30 padding-bottom-60">
            <div class="row">
                {!! render_frontend_sidebar('footer_top',['column' => true]) !!}
            </div>
        </div>
        <div class="footer-middle footer-middle-border padding-top-30 padding-bottom-60">
            <div class="row">
                {!! render_frontend_sidebar('footer',['column' => true]) !!}
            </div>
        </div>
        <div class="copyright-area copyright-border">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="copyright-contents">
                        {!! get_footer_copyright_text() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
