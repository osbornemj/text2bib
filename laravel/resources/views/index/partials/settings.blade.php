item separator = {{ $conversion->item_separator}}
&nbsp;&bull;&nbsp;
language = {{ $conversion->language }}
&nbsp;&bull;&nbsp;
labels = {{ $conversion->label_style }}
&nbsp;&bull;&nbsp;
line endings = {{ $conversion->line_endings }}
<br/>
@if ($conversion->char_encoding == 'utf8')
    convert accents to TeX
@elseif ($conversion->char_encoding == 'utf8leave')
    do not convert accents to TeX
@elseif ($conversion->char_encoding == 'utf8force')
    assume UTF-8 even if PHP says encoding is not UTF-8
@endif
&nbsp;&bull;&nbsp;
{{ $conversion->percent_comment ? '% = comment' : '% != comment' }}
&nbsp;&bull;&nbsp;
{{ $conversion->include_source ? 'include source' : 'no source' }}
&nbsp;&bull;&nbsp;
{{ $conversion->report_type }} report
@if ($conversion->use)
  <br/>
  to be used for = 
  @if ($conversion->use == "other")
    {{ $conversion->other_use }}
  @else
    {{ $conversion->use }}
  @endif
@endif
