@php
$line_shape = get_static_option('highlight_text_shape');
$highlighted_image = get_attachment_image_by_id($line_shape);
$highlighted_image = !empty($highlighted_image) ? $highlighted_image['img_url'] : '';
@endphp
<style>
    .title-shape::before {
        background-image: url("{{$highlighted_image}}") !important;
    }
</style>
