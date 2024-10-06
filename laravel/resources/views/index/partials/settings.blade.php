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
              According to our data, the <code>{{ $conversion->bst->name }}</code> BibTeX style
            </p>
              <x-list>
                <li class="ml-6">
                  @if ($conversion->bst->doi) 
                    supports
                  @else
                    does not support
                  @endif
                  the <code>doi</code> field
                </li>
                <li class="ml-6">
                  @if ($conversion->bst->url) 
                    supports
                  @else
                    does not support
                  @endif
                  the <code>url</code> field
                </li>
                <li class="ml-6">
                  @if ($conversion->bst->urldate) 
                    supports
                  @else
                    does not support
                  @endif
                  the <code>urldate</code> field
                </li>
                <li class="ml-6">
                  @if ($conversion->bst->translator) 
                    supports
                  @else
                    does not support
                  @endif
                  the <code>translator</code> field
                </li>
                <li class="ml-6">
                  @if ($conversion->bst->online) 
                    supports
                  @else
                    does not support
                  @endif
                  the <code>@online</code> item type
                </li>
                @if ($conversion->bst->doi)
                  <li class="ml-6">
                    @if ($conversion->bst->doi_escape_underscore) 
                      requires
                    @else
                      does not require
                    @endif
                    underscores in <code>doi</code>s to be escaped
                  </li>
                @endif
                <li class="ml-6">
                  @if ($conversion->bst->proc_address_conf_location) 
                    interprets the <code>address</code> field for an <code>inproceedings</code> item as the location of the conference.
                  @else
                    interprets the <code>address</code> field for an <code>inproceedings</code> item as the city of publication of the proceedings.
                  @endif
                </li>
              </x-list>
              <p>
                Content relating to unsupported fields is included in the <code>notes</code> field.
              </p>
        @else
        <p>
          @if ($conversion->bst->available)
            We have not yet examined the <code>{{ $conversion->bst->name }}</code> BibTeX style.  We will do so as time permits.
          @else
            We have been unable to find the <code>{{ $conversion->bst->name }}</code> BibTeX style.  
          @endif
          The conversion algorithm assumes that, like most, but not all, styles, it does not support the <code>doi</code>, <code>url</code>, <code>urldate</code>, or <code>translator</code> fields or the <code>@online</code> item type, requires underscores in <code>doi</code>s to be escaped, and treats the <code>address</code> field for an <code>inproceedings</code> item as the city of publication.
        </p>
        @endif
    @endif
  @endif
@endif
