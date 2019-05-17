<?php declare(strict_types = 1);

namespace Diglabby\Doika\Http\Controllers\Dashboard;

use Diglabby\Doika\Http\Controllers\Controller;
use Diglabby\Doika\Http\Resources\Dashboard\CampaignResource;
use Diglabby\Doika\Models\Campaign;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

final class CampaignController extends Controller
{
    public function index(): \JsonSerializable
    {
        $query = Campaign::query()
            ->with('transactions') // to calc sum of transactions
            ->withCount(['subscriptions', 'transactions']);

        return CampaignResource::collection(
            QueryBuilder::for($query)->paginate()
        );
    }

    public function show(Campaign $campaign): \JsonSerializable
    {
        return new CampaignResource($campaign);
    }

    public function store(Request $request): \JsonSerializable
    {
        $this->validate($request, [
            'title' => ['required', 'max:255'],
            'target_amount' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'size:3'],
            'started_at' => ['required', 'date'],
            'finished_at' => ['required', 'date', 'after:started_at'],
        ]);

        $campaign = new Campaign($request->all());
        $campaign->save();

        return $campaign;
    }

    public function update(Campaign $campaign, Request $request): \JsonSerializable
    {
        $campaign->update($request->all());
        return $campaign;
    }

    public function delete(Campaign $campaign): \JsonSerializable
    {
        $campaign->delete();
        return $campaign;
    }
}
