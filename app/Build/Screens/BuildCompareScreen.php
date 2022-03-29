<?php

namespace App\Build\Screens;

use App\Build\Layouts\BuildCompareLayout;
use App\Common\Screen\Screen;
use App\Models\Build;
use App\Models\ListFileBuilds;
use Illuminate\Http\Request;

class BuildCompareScreen extends Screen
{
    /** @inerhitDoc */
    public $name = 'Build Compare';

    /** @inerhitDoc */
    public $description = 'A comparison of two builds';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Request $request): array
    {
        $request->validate([
            'oldBuild' => 'int|required',
            'newBuild' => 'int|required',
        ]);

        $oldBuildId = $request->query('oldBuild');
        $newBuildId = $request->query('newBuild');

        $oldBuild = Build::query()->findOrFail($oldBuildId);
        $newBuild = Build::query()->findOrFail($newBuildId);

        return [
            'buildCompare' => $this->compareBuild($oldBuild, $newBuild)
        ];
    }

    /** @inerhitDoc */
    public function layout(): array
    {
        return [
            BuildCompareLayout::class,
        ];
    }

    /**
     * Compares two builds, and returns the added, removed, and modified files.
     *
     * @return array
     */
    public function compareBuild(Build $oldBuild, Build $newBuild): array
    {
        $oldListfileBuildCount = ListFileBuilds::query()
            ->where('buildId', $oldBuild->id)
            ->count();

        $newListfileBuildCount = ListFileBuilds::query()
            ->where('buildId', $newBuild->id)
            ->count();
            
        dd('Builds have different number of listfile builds', $oldListfileBuildCount, $newListfileBuildCount);
        if ($oldListfileBuildCount != $newListfileBuildCount) {
        }

        return [];
    }
}