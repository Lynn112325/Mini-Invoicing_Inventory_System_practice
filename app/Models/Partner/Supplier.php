<?php
class Supplier extends BasePartner
{
    protected $table = 'suppliers';
    protected $childFields = ['tax_id', 'payment_terms', 'bank_account'];
    protected $roleColumn = 'is_supplier';

    public function getPaginated($search, $filters, $paginationParams)
    {
        // Extra filters specific to suppliers
        static $extraFiltersConfig = [
            'terms' => ['col' => 'c.payment_terms', 'type' => 'string'],
        ];

        static $allowedSort = [
            'name'          => 'p.name',
            'tax_id'        => 'c.tax_id',
            'payment_terms' => 'c.payment_terms',
            'bank_account'  => 'c.bank_account',
            'date'          => 'p.created_at'
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

            // 2. Insert into Suppliers
            $stmt = $this->db->prepare("INSERT INTO suppliers (partner_id, tax_id, payment_terms) 
                                        VALUES (?, ?, ?)");
            $stmt->execute([$partnerId, $data['tax_id'], $data['payment_terms']]);

            return $partnerId;
        });
    }

    public function update($id, $data)
    {
        return $this->executeTransaction(function () use ($id, $data) {
            // 1. Update Parent
            $this->updateParent($id, $data);

            // 2. Update Child
            $sql = "UPDATE {$this->table} SET tax_id = ?, payment_terms = ?, bank_account = ? WHERE partner_id = ?";
            $this->db->prepare($sql)->execute([
                $data['tax_id'],
                $data['payment_terms'],
                $data['bank_account'],
                $id
            ]);

            return true;
        });
    }

    public function softDelete($id)
    {
        $this->delete($id);
    }
}
