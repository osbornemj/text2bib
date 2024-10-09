<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Bst;

class BstFileController extends Controller
{
    public function index(Request $request): View
    {
        $bsts = Bst::where('available', 1);

        $input = $request->except('_token');
        if (! empty($input)) {
            $types = ['author-date', 'numeric', 'other'];
            foreach ($types as $type) {
                if (! isset($input[$type])) {
                    $bsts = $bsts->where('type', '!=', $type);
                }
            }

            $fields = ['doi', 'eid', 'isbn', 'issn', 'translator', 'url', 'urldate'];
            foreach ($fields as $field) {
                if (isset($input[$field])) {
                    $bsts = $bsts->where($field, 1);
                }
            }
        }

        $bsts = $bsts->orderBy('name')
            ->paginate(50);

        return view('bsts', compact('bsts', 'types', 'fields', 'input'));
    }
    
}
