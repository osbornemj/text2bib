<i>Settings</i>: item separator = {{ $conversion->item_separator}}
&nbsp;&bull;&nbsp;
language = {{ $conversion->language }}
&nbsp;&bull;&nbsp;
labels = {{ $conversion->label_style }}
&nbsp;&bull;&nbsp;
line endings = {{ $conversion->line_endings }}
<br/>
@if ($conversion->char_encoding == 'utf8')
    convert accents to TeX
@else
    do not convert accents to TeX
@endif
&nbsp;&bull;&nbsp;
{{ $conversion->percent_comment ? '% = comment' : '% != comment' }}
&nbsp;&bull;&nbsp;
{{ $conversion->include_source ? 'include source' : 'no source' }}
&nbsp;&bull;&nbsp;
{{ $conversion->report_type }} report
