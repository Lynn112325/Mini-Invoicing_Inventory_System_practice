<?php
class Customer extends BasePartner
{
    protected $table = 'customers';
    protected $childFields = ['credit_limit', 'customer_level'];

    public function getPaginated($filters)
    {
        $extraFiltersConfig = [
            'customer_level' => ['col' => 'c.customer_level', 'type' => 'string'],
            'credit_min' => ['col' => 'c.credit_limit',   'op' => '>=', 'type' => 'float'],
            'credit_max' => ['col' => 'c.credit_limit',   'op' => '<=', 'type' => 'float'],
        ];

        $selectSql = "SELECT p.*, c.credit_limit, c.customer_level";
        $fromWhereSql = "FROM partners p INNER JOIN customers c ON p.id = c.partner_id";

        $allowedSort = ['p.name', 'p.email', 'c.customer_level', 'c.credit_limit', 'p.created_at'];

        return $this->getPaginatedPartners($selectSql, $fromWhereSql, $filters, [], $extraFiltersConfig, $allowedSort);
    }

    public function create($data)
    {
        return $this->executeTransaction(function () use ($data) {
            // 1. Insert into Partners
            $stmt = $this->db->prepare("INSERT INTO partners (name, email, phone, address, type) 
                                        VALUES (?, ?, ?, ?, 'customer')");
            $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['address']]);
            $partnerId = $this->db->lastInsertId();

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
}
