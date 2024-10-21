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
    @if ($conversion->use == 'latex' && $conversion->bst_id)
      (<code>{{ $conversion->bst->name }}</code>)
      @if ($conversion->bst->checked && $conversion->bst->available)
        <p>
          According to my data, the <code>{{ $conversion->bst->name }}</code> BibTeX style has the following support for nonstandard fields (<span class="positive">field</span> = supported, <span class="negative">field</span> = unsupported):
          @foreach ($bstFields as $field)
            <span @if ($conversion->bst->$field) class="positive" @else class="negative" @endif>{{ $field }}</span>@if ($loop->last).@endif
          @endforeach
          The conversion algorithm writes content relating to unsupported fields to the <code>note</code> field.
          The style
          @if ($conversion->bst->online) 
            supports
          @else
            does not support
          @endif
          the <code>@online</code> item
          @if ($conversion->bst->doi)
            type,
            @if ($conversion->bst->doi_escape_underscore) 
              requires
            @else
              does not require
            @endif
            underscores in <code>doi</code>s to be escaped,
          @else
            type
          @endif  
          and
          @if ($conversion->bst->proc_address_conf_location) 
            interprets the <code>address</code> field for an <code>inproceedings</code> item as the location of the conference.
          @else
            interprets the <code>address</code> field for an <code>inproceedings</code> item as the city of publication of the proceedings.
          @endif
        </p>
      @else
        <p>
          @if ($conversion->bst->available)
            I have not yet examined the <code>{{ $conversion->bst->name }}</code> BibTeX style.  I will do so as time permits.
          @else
            I have been unable to locate the <code>{{ $conversion->bst->name }}</code> BibTeX style on a public website.  
          @endif
          The conversion algorithm has assumed that, like most styles, it does not support the <code>doi</code>, <code>url</code>, <code>urldate</code>, or <code>translator</code> fields or the <code>@online</code> item type, requires underscores in <code>doi</code>s to be escaped, and treats the <code>address</code> field for an <code>inproceedings</code> item as the city of publication.
        </p>
      @endif
    @endif
  @endif
@endif
