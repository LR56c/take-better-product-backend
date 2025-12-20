<?php

namespace Src\Countries\Application;

use InvalidArgumentException;
use Src\Countries\Domain\Country;
use Src\Countries\Domain\CountryRepository;
use Src\Countries\Domain\Exceptions\CountryNotFound;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class UpdateCountry
{
    public function __construct(
        private readonly CountryRepository $repository
    ) {}

    public function execute(string $id, array $data): Country
    {
        $countryId = ValidUUID::from($id);

        if ($countryId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The country id <%s> is invalid', $id));
        }

        $country = $this->repository->find($countryId);

        if (null === $country) {
            throw new CountryNotFound($countryId);
        }

        $data = array_filter($data, fn($value) => $value !== null);
        $country->fill($data);

        $this->repository->save($country);

        return $country;
    }
}
