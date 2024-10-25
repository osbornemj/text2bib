<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Models\Bst;

class BstFileController extends Controller
{
    var $nonstandardFields;

    public function __construct()
    {
        $this->nonstandardFields = config('constants.nonstandard_fields');
    }

    public function index(Request $request): View
    {
        $bsts = Bst::where('available', 1);
        $types = ['author-date', 'numeric', 'other'];
        $nonstandardFields = $this->nonstandardFields;

        $input = $request->except('_token');
        if (! empty($input)) {
            foreach ($types as $type) {
                if (! isset($input[$type])) {
                    $bsts = $bsts->where('type', '!=', $type);
                }
            }

            foreach ($nonstandardFields as $field) {
                if (isset($input[$field])) {
                    $bsts = $bsts->where($field, 1);
                }
            }
        }

        $bsts = $bsts->orderBy('name')
            ->paginate(50);

        return view('bsts', compact('bsts', 'types', 'nonstandardFields', 'input'));
    }
    
}
