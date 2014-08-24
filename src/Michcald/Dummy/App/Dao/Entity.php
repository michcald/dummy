<?php

namespace Michcald\Dummy\App\Dao;

class Entity extends \Michcald\Dummy\Dao
{
    private $repository;

    public function setRepository(\Michcald\Dummy\App\Model\Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    public function getTable()
    {
        return $this->repository->getName();
    }

    public function create(array $row = null)
    {
        $entity = new \Michcald\Dummy\App\Model\Entity();
        $entity->setRepository($this->repository);

        if ($row) {
            if (isset($row['id'])) {
                $entity->setId($row['id']);
                unset($row['id']);
            }

            $entity->setValues($row);

            if ($entity->getId()) {
                // file handling

                $query = new \Michcald\Dummy\Dao\Query();
                $query->addWhere('repository_id', $this->repository->getId())
                    ->addOrder('display_order', 'ASC')
                    ->setLimit(10000);
                $repositoryFieldDao = new Repository\Field();
                $fields = $repositoryFieldDao->findAll($query);

                foreach ($fields->getResults() as $field) {

                    $fieldName = $field->getName();
                    if ($field->getType() == 'file') {
                        $entity->$fieldName = sprintf(
                            '%spub/uploads/%d/%s',
                            \Michcald\Dummy\Config::getInstance()->base_url,
                            $this->repository->getId(),
                            $row[$fieldName]
                        );
                    }
                }
            }

        }

        return $entity;
    }



    public function persist($entity)
    {
        /* @var $entity \Michcald\Dummy\App\Model\Entity */

        $fieldDao = new Repository\Field();

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('repository_id', $this->repository->getId());

        $result = $fieldDao->findAll($query);

        foreach ($result->getResults() as $field) {

            /* @var $field \Michcald\Dummy\App\Model\Repository\Field */
            if ($field->getType() == 'file') {

                $uploadDir = __DIR__ . '/../../../../../pub/uploads/' . $this->repository->getId();

                if (!is_dir($uploadDir)) {
                    $res = mkdir($uploadDir, 0777);
                    if (!$res) {
                        throw new \Exception('Need to set 777 on uploads dir');
                    }
                }

                $entityArray = $entity->toArray();
                $fileArray = $entityArray[$field->getName()];

                $fileExt = pathinfo($fileArray['name'], PATHINFO_EXTENSION);

                $newFilename = md5(uniqid(rand(), true)) . '.' . $fileExt;
                $newFilePath = $uploadDir . '/' . $newFilename;

                $res = move_uploaded_file($fileArray['tmp_name'], $newFilePath);

                if (!$res) {
                    throw new \Exception('Cannot save file');
                }

                $key = $field->getName();
                $entity->$key = $newFilename;
            }
        }

        return parent::persist($entity);
    }

    public function delete($entity)
    {
        /* @var $entity \Michcald\Dummy\App\Model\Entity */
        $entityArray = $entity->toArray();

        $fieldDao = new Repository\Field();

        $query = new \Michcald\Dummy\Dao\Query();
        $query->addWhere('repository_id', $this->repository->getId());

        $result = $fieldDao->findAll($query);

        foreach ($result->getResults() as $field) {
            /* @var $field \Michcald\Dummy\App\Model\Repository\Field */
            if ($field->getType() == 'file') {

                $filePath = __DIR__ . '/../../../../../pub/uploads/' . $this->repository->getId() . '/' . $entityArray[$field->getName()];

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        parent::delete($entity);
    }
}
