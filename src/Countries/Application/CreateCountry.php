<?php

namespace Src\Countries\Application;

use Src\Countries\Domain\Country;
use Src\Countries\Domain\CountryRepository;

class CreateCountry
{
    public function __construct(
        private readonly CountryRepository $repository
    ) {}

    public function execute(array $data): Country
    {
        $country = new Country();
        $country->fill($data);

        $this->repository->save($country);

        return $country;
    }
}
