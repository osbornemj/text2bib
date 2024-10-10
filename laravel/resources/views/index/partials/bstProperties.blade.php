@if ($bst->checked && $bst->available)
<p class="mb-1">
  Type: {{ $bst->type}}
  @if ($bst->style_required)
    (requires {{$bst->style_required}} LaTeX style)
  @endif
</p>
  <p class="mt-0">
    @foreach ($fields as $field)
      <span @if ($bst->$field) class="positive" @else class="negative" @endif>{{ $field }}</span>,
    @endforeach
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
  <div class="mb-2">
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
  </div>
@else
<p>
  @if ($bst->available)
    I have not yet examined the <code>{{ $bst->name }}</code> style.  I will do so as time permits.
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

