@if ($bst->checked && $bst->available)
<p class="mb-1">
  Type: {{ $bst->type}}
  @if ($bst->style_required)
    (requires {{$bst->style_required}} LaTeX style)
  @endif
</p>
  <p class="mt-0">
    <span @if ($bst->doi) class="positive" @else class="negative" @endif>doi</span>,
    <span @if ($bst->url) class="positive" @else class="negative" @endif>url</span>,
    <span @if ($bst->urldate) class="positive" @else class="negative" @endif>urldate</span>,
    <span @if ($bst->translator) class="positive" @else class="negative" @endif>translator</span>,
    <span @if ($bst->eid) class="positive" @else class="negative" @endif>eid</span>,
    <span @if ($bst->isbn) class="positive" @else class="negative" @endif>isbn</span>,
    <span @if ($bst->issn) class="positive" @else class="negative" @endif>issn</span>.
  </p>
  <p class="ml-0">
    @if ($bst->online) 
      Supports
    @else
      Does not support
    @endif
    the <code>@online</code> item type.
  </p>
  @if ($bst->doi)
    <p class="ml-0">
      @if ($bst->doi_escape_underscore) 
        Requires
      @else
        Does not require
      @endif
      underscores in <code>doi</code>s to be escaped.
    </p>
  @endif
  <p class="ml-0">
    Interprets the <code>address</code> field for an <code>inproceedings</code> item as the 
      @if ($bst->proc_address_conf_location) 
        location of the conference.
      @else
        city of publication of the proceedings.
      @endif
  </p>
  @if ($bst->file_url)
    <p>
      Available at <x-link href="{{ $bst->file_url }}" target="_blank">{{ $bst->file_url}}</x-link>
    </p>
  @endif
@else
<p>
  @if ($bst->available)
    I have not yet examined the <code>{{ $bst->name }}</code> BibTeX style.  I will do so as time permits.
  @else
    I have been unable to find the <code>{{ $bst->name }}</code> BibTeX style.  
  @endif
  The conversion algorithm has assumed that, like most styles, it does not support the <code>doi</code>, <code>url</code>, <code>urldate</code>, or <code>translator</code> fields or the <code>@online</code> item type, requires underscores in <code>doi</code>s to be escaped, and treats the <code>address</code> field for an <code>inproceedings</code> item as the city of publication.
</p>
@endif

@if ($bst->note)
<p>
  {{ $bst->note }}
</p>
@endif

