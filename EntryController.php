<?php

namespace App\Overrides\Telescope;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Telescope\Contracts\EntriesRepository;
use Laravel\Telescope\Storage\EntryQueryOptions;

abstract class EntryController extends Controller
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    abstract protected function entryType();

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    abstract protected function watcher();

    /**
     * List the entries of the given type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Telescope\Contracts\EntriesRepository  $storage
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, EntriesRepository $storage)
    {   
        $tag_val = '';
        if ($request->tag != '' && !strpos($request->tag, ':') !== false) {
            $tag_val = $request->tag;
            $request->merge(['tag' => '']);
        }

        $entries = $storage->get(
                $this->entryType(),
                EntryQueryOptions::fromRequest($request)
            );

        $val = array();
        if ($tag_val != '') {
            foreach (json_decode($entries) as $key => $value) {

                if (strpos(json_encode($value), $tag_val) !== false) {

                    array_push($val, $value);
                }
            }

            $entries = $val;
        }

        return response()->json([
            'entries' => $entries,
            'status' => $this->status(),
        ]);
    }

    /**
     * Get an entry with the given ID.
     *
     * @param  \Laravel\Telescope\Contracts\EntriesRepository  $storage
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(EntriesRepository $storage, $id)
    {
        $entry = $storage->find($id)->generateAvatar();

        return response()->json([
            'entry' => $entry,
            'batch' => $storage->get(null, EntryQueryOptions::forBatchId($entry->batchId)->limit(-1)),
        ]);
    }

    /**
     * Determine the watcher recording status.
     *
     * @return string
     */
    protected function status()
    {
        if (! config('telescope.enabled', false)) {
            return 'disabled';
        }

        if (cache('telescope:pause-recording', false)) {
            return 'paused';
        }

        $watcher = config('telescope.watchers.'.$this->watcher());

        if (! $watcher || (isset($watcher['enabled']) && ! $watcher['enabled'])) {
            return 'off';
        }

        return 'enabled';
    }
}
