<?php

namespace SSone\CMSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FormRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CMSFormRepository extends EntityRepository
{

    public function findBySecurekey($securekey)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c FROM SSoneCMSBundle:CMSForm c WHERE c.securekey = :securekey'
            )->setParameter('securekey', $securekey)
            ->getSingleResult();
    }


    public function getItemsForListTable()
    {
        $data['type'] = "Field Types";
        $data['columns'] = array(
            "Name"=>"name",
            "Send Admin Email"=>"sendAdminEmailOnSubmit",
            "Email to"=>"adminEmailToAddress",
            "Last Modified"=>"modifiedAt",
            "Last Modified By"=>"modifiedBy",
        );
        $data['items'] = $this->getEntityManager()
            ->createQuery(
                'SELECT
                c.name,
                c.id,
                c.modifiedAt,
                c.modifiedBy,
                c.securekey,
                c.sendAdminEmailOnSubmit,
                c.adminEmailToAddress,
                ct.name as contantType
                FROM SSoneCMSBundle:CMSForm c
                LEFT JOIN c.contentType ct
                ORDER BY c.name ASC'
            )
            ->getResult();


        foreach($data['items'] as &$item)
        {
            if($item['sendAdminEmailOnSubmit'] == 1)
            {
                $item['sendAdminEmailOnSubmit'] = "Yes";
            }
            else
            {
                $item['sendAdminEmailOnSubmit'] = "No";
            }
        }

        return $data;
    }


    public function getCMSFormByFormId($id,$localiser)
    {
        $CMSForm = $this->getEntityManager()
            ->createQuery(
                'SELECT
                f.formTitle,
                f.successURL,
                f.template,
                f.buttonText,
                f.sendAdminEmailOnSubmit,
                f.adminEmailToAddress,
                f.adminEmailFromAddress,
                f.adminEmailText,
                f.adminEmailHTML,
                f.formTitle,
                ct.id AS contentTypeId
                FROM SSoneCMSBundle:CMSForm f
                LEFT JOIN f.contentType ct
                WHERE f.id = :id'
            )->setParameter('id', $id)
            ->getSingleResult();

        $CMSForm = $localiser->setMultiLanguageFields($CMSForm,
            array(
                "title",
                "formPre",
                "formPost",
                "successURL",
                "formTitle",
            ),
            $localiser->locale
        );

        return $CMSForm;
    }
}