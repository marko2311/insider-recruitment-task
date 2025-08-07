<?php

declare(strict_types=1);

namespace App\Validator\Game;

use App\Dto\Game\UpdateGameResultDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

readonly class UpdateGameResultValidator implements UpdateGameResultValidatorInterface
{
    public function __construct(
        private ValidatorInterface $validator
    ) {}

    public function validate(UpdateGameResultDTO $dto): void
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            throw new UnprocessableEntityHttpException(json_encode(['errors' => $errors]));
        }
    }
}
