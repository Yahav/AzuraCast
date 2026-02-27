<?php

declare(strict_types=1);

namespace App\Entity\Fixture;

use App\Entity\Podcast;
use App\Entity\PodcastCategory;
use App\Entity\Station;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class PodcastFixture extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $station = $this->getReference('station', Station::class);

        $podcastStorage = $station->podcasts_storage_location;

        $podcast = new Podcast($podcastStorage);

        $podcast->title = 'The Caster.fm Podcast';
        $podcast->link = 'https://www.caster.fm';
        $podcast->language = 'en';
        $podcast->description = 'The unofficial testing podcast for the Caster.fm development team.';
        $podcast->author = 'Casterfm';
        $podcast->email = 'demo@caster.fm';
        $podcast->explicit = false;
        $manager->persist($podcast);

        $category = new PodcastCategory($podcast, 'Technology');
        $manager->persist($category);

        $manager->flush();

        $this->setReference('podcast', $podcast);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            StationFixture::class,
        ];
    }
}
