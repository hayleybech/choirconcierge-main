<?php

namespace App\Support\VisualRegression;

use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;

class ScreenshotCatalog
{
    /**
     * @return list<array{
     *     id: string,
     *     module: string,
     *     page_type: string,
     *     state: string,
     *     route: string,
     *     parameters: array<string, string|int>,
     *     persona: string,
     *     component: string,
     *     browser: string,
     *     assert_text: string,
     *     empty_props?: list<string>,
     *     pdf_viewer?: bool
     * }>
     *
     * @throws JsonException
     */
    public function cases(): array
    {
        $cases = $this->contents()['cases'] ?? null;

        if (! is_array($cases) || ! array_is_list($cases)) {
            throw new RuntimeException('The visual-regression catalog must define a cases list.');
        }

        $caseIds = [];

        foreach ($cases as $case) {
            $id = is_array($case) ? ($case['id'] ?? null) : null;

            if (! is_string($id) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id) !== 1) {
                throw new RuntimeException('Visual-regression case IDs must be safe lowercase path segments.');
            }

            if (in_array($id, $caseIds, true)) {
                throw new RuntimeException('Visual-regression case IDs must be unique.');
            }

            $caseIds[] = $id;
        }

        return $cases;
    }

    /**
     * @return list<array{id: string, label: string, width: int, height: int}>
     *
     * @throws JsonException
     */
    public function viewports(): array
    {
        $viewports = $this->contents()['viewports'] ?? null;

        if (! is_array($viewports) || ! array_is_list($viewports)) {
            throw new RuntimeException('The visual-regression catalog must define a viewports list.');
        }

        $viewportIds = [];

        foreach ($viewports as $viewport) {
            $id = is_array($viewport) ? ($viewport['id'] ?? null) : null;

            if (! is_string($id) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $id) !== 1) {
                throw new RuntimeException('Visual-regression viewport IDs must be safe lowercase path segments.');
            }

            if (in_array($id, $viewportIds, true)) {
                throw new RuntimeException('Visual-regression viewport IDs must be unique.');
            }

            $viewportIds[] = $id;
        }

        return $viewports;
    }

    /**
     * @return array{
     *     id: string,
     *     module: string,
     *     page_type: string,
     *     state: string,
     *     route: string,
     *     parameters: array<string, string|int>,
     *     persona: string,
     *     component: string,
     *     browser: string,
     *     assert_text: string,
     *     empty_props?: list<string>,
     *     pdf_viewer?: bool
     * }|null
     *
     * @throws JsonException
     */
    public function find(string $id): ?array
    {
        foreach ($this->cases() as $case) {
            if ($case['id'] === $id) {
                return $case;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function contents(): array
    {
        $catalogPath = config('visual-regression.catalog_path');

        if (! is_string($catalogPath) || ! File::exists($catalogPath)) {
            throw new RuntimeException('The visual-regression catalog file does not exist.');
        }

        $contents = json_decode(File::get($catalogPath), true, flags: JSON_THROW_ON_ERROR);

        if (! is_array($contents) || ($contents['schema_version'] ?? null) !== 1) {
            throw new RuntimeException('The visual-regression catalog has an unsupported schema.');
        }

        return $contents;
    }
}
