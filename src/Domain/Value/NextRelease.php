<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Domain\Value;

use App\Github\Domain\Value\CombinedStatus;
use App\Github\Domain\Value\PullRequest;
use App\Github\Domain\Value\Release\Tag;

/**
 * @author Oskar Stark <oskarstark@googlemail.com>
 */
final class NextRelease
{
    private Project $project;
    private Tag $currentTag;
    private Tag $nextTag;
    private CombinedStatus $combinedStatus;

    /**
     * @var PullRequest[]
     */
    private array $pullRequests;

    private function __construct(
        Project $project,
        Tag $currentTag,
        Tag $nextTag,
        CombinedStatus $combinedStatus,
        array $pullRequests
    ) {
        $this->project = $project;
        $this->currentTag = $currentTag;
        $this->nextTag = $nextTag;
        $this->combinedStatus = $combinedStatus;
        $this->pullRequests = $pullRequests;
    }

    /**
     * @param PullRequest[] $pullRequests
     */
    public static function fromValues(
        Project $project,
        Tag $currentTag,
        Tag $nextTag,
        CombinedStatus $combinedStatus,
        array $pullRequests
    ): self {
        return new self(
            $project,
            $currentTag,
            $nextTag,
            $combinedStatus,
            $pullRequests
        );
    }

    public function project(): Project
    {
        return $this->project;
    }

    public function currentTag(): Tag
    {
        return $this->currentTag;
    }

    public function nextTag(): Tag
    {
        return $this->nextTag;
    }

    public function combinedStatus(): CombinedStatus
    {
        return $this->combinedStatus;
    }

    /**
     * @return PullRequest[]
     */
    public function pullRequests(): array
    {
        return $this->pullRequests;
    }

    public function changelog(): Changelog
    {
        return Changelog::fromPullRequests(
            $this->pullRequests,
            $this->nextTag,
            $this->currentTag,
            $this->project->package()
        );
    }

    public function isNeeded(): bool
    {
        if ($this->project->package()->isAbandoned()) {
            return false;
        }

        return $this->nextTag->toString() !== $this->currentTag->toString();
    }
}
