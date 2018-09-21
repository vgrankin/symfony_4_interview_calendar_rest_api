<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

abstract class BaseService
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Validate data and get violations (if any)
     *
     * @param array $data which contains data to validate
     * @param array $rules Specifies which keys in data and how to validate. All keys will be validated by default.
     * @param array $options Additional options to use during validation
     * @return ConstraintViolationListInterface
     */
    protected function getViolations(array $data, array $rules = [], $options = []): ConstraintViolationListInterface
    {
        $validator = Validation::createValidator();
        $params = [
            'fields' => $rules,
            // even though it's anomaly, currently we don't care if there are unrelated fields in $data
            'allowExtraFields' => true
        ];
        foreach ($options as $optionName => $optionValue) {
            $params[$optionName] = $optionValue;
        }
        $constraint = new Assert\Collection($params);
        $violations = $validator->validate($data, $constraint);

        return $violations;
    }

    /**
     * Convert array of violations (if any) to string with specified delimiter
     *
     * @param $violations
     * @return string
     */
    protected function getErrorsStr($violations): string
    {
        $errorDelimiter = "###";
        $errors = [];
        foreach ($violations as $violation) {
            $errorMessage = $violation->getMessage();
            $errors[] = $errorMessage;
        }
        $errors = implode($errorDelimiter, $errors);
        return $errors;
    }
}