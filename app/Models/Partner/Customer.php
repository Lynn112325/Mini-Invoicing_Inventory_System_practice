<?php
class Customer extends BasePartner
{
    protected $table = 'customers';
    protected $childFields = ['credit_limit', 'customer_level'];
    protected $roleColumn = 'is_customer';

    public function getPaginated($search, $filters, $paginationParams)
    {
        static $extraFiltersConfig = [
            'customer_level' => ['col' => 'c.customer_level', 'type' => 'string'],
            'credit_min' => ['col' => 'c.credit_limit',   'op' => '>=', 'type' => 'float'],
            'credit_max' => ['col' => 'c.credit_limit',   'op' => '<=', 'type' => 'float'],
        ];

        static $allowedSort = [
            'name'   => 'p.name',
            'level'  => 'c.customer_level',
            'credit' => 'c.credit_limit',
            'date'   => 'p.created_at'
        ];

        return $this->getPaginatedPartners($filters, $search, $paginationParams, [], $extraFiltersConfig, $allowedSort);
    }

    public function create($data, $existingPartnerId = null)
    {
        return $this->executeTransaction(function () use ($data, $existingPartnerId) {
            // 1. Insert into Partners
            if ($existingPartnerId) {
                $partnerId = $existingPartnerId;
                $this->enableNewRole($partnerId, $this->roleColumn);
            } else {
                $partnerId = $this->createBasePartner($data, $this->roleColumn);
            }

            // 2. Insert into Customers
            $stmt = $this->db->prepare("INSERT INTO customers (partner_id, credit_limit, customer_level) 
                                        VALUES (?, ?, ?)");
            $stmt->execute([$partnerId, $data['credit_limit'], $data['customer_level']]);

            return $partnerId;
        });
    }

    public function update($id, $data)
    {
        return $this->executeTransaction(function () use ($id, $data) {
            // 1. Update Parent
            $this->updateParent($id, $data);

            // 2. Update Child
            $sql = "UPDATE customers SET credit_limit = ?, customer_level = ? WHERE partner_id = ?";
            $this->db->prepare($sql)->execute([$data['credit_limit'], $data['customer_level'], $id]);

            return true;
        });
    }

    public function softDelete($id)
    {
        $this->delete($id);
    }

    public function getCustomerLevels()
    {
        return $this->getEnumValues('customer_level');
    }
}
