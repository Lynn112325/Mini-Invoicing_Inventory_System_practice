<?php
abstract class BasePartner
{
    protected $db;
    protected $table;      // Defined in child (e.g., 'customers')
    protected $childFields = []; // Defined in child (e.g., ['credit_limit', 'customer_level'])
    protected $roleColumn;   // Defined in child (e.g., 'is_customer' or 'is_supplier')

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get a paginated and filtered list of partners with associated child table data.
     * 
     * @param array $filters           Raw filter inputs from the request
     * @param string $search           The global search keyword
     * @param array $paginationParams  Pagination and sorting configuration (page, sort, order)
     * @param array $extraSearchColumns Additional child table columns to include in global search
     * @param array $extraFiltersConfig Specific filtering rules defined by the child model
     * @param array $allowedSort       Key-value mapping of valid sort keys and their SQL columns
     * @return array                   Paginated data, totals, and current state for the view
     */
    protected function getPaginatedPartners($filters, $search, $paginationParams, $extraSearchColumns = [], $extraFiltersConfig = [], $allowedSort = [])
    {
        // 1. Automatically build SELECT clause based on child fields
        $childSelects = empty($this->childFields) ? '' : ', c.' . implode(', c.', $this->childFields);
        $selectSql = "SELECT p.*" . $childSelects;

        // 2. Automatically build FROM clause with INNER JOIN
        $fromWhereSql = "FROM partners p INNER JOIN {$this->table} c ON p.id = c.partner_id";

        // 3. Initialize default query conditions and parameters
        $conditions = [
            "p.deleted_at IS NULL",
            "p.{$this->roleColumn} = 1"
        ];
        $params = [];

        // 4. Process dynamic filters defined by the child model
        foreach ($extraFiltersConfig as $key => $val) {
            if (isset($filters[$key]) && $filters[$key] !== '') {
                $op = $val['op'] ?? '=';
                $paramName = "filter_" . $key;
                $conditions[] = "{$val['col']} {$op} :{$paramName}";

                // Cast parameter value based on its strict data type
                if ($val['type'] === 'int') $params[$paramName] = (int)$filters[$key];
                elseif ($val['type'] === 'float') $params[$paramName] = (float)$filters[$key];
                else $params[$paramName] = $filters[$key];
            }
        }

        // 5. Combine the base query with all dynamic WHERE clauses
        $fullFromWhere = $fromWhereSql . " WHERE " . implode(" AND ", $conditions);

        // 6. Set up columns available for the global search keyword
        $searchConfig = array_merge(['p.name', 'p.email', 'p.phone', 'p.address'], $extraSearchColumns);

        // 7. Delegate executing and tokenizing pagination data to the helper
        require_once '../config/functions/pagination_helper.php';
        return getPaginatedData($this->db, $selectSql, $fullFromWhere, $searchConfig, $allowedSort, $paginationParams, $search, $params);
    }

    /**
     * Get a single record with joined child data
     */
    public function find($id)
    {
        $sql = "SELECT p.*, c.* 
                FROM partners p 
                JOIN {$this->table} c ON p.id = c.partner_id 
                WHERE p.id = :id AND p.deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Logical Delete (Soft Delete)
     * Since DB has ON DELETE CASCADE, a hard delete here 
     * would wipe the child record too.
     */
    public function delete($id)
    {
        $sql = "UPDATE partners SET deleted_at = NOW() WHERE id = :id";
        return $this->db->prepare($sql)->execute(['id' => $id]);
    }

    /**
     * Helper to update the parent 'partners' table
     */
    protected function updateParent($id, $data)
    {
        $sql = "UPDATE partners SET 
                name = :name, 
                email = :email, 
                phone = :phone, 
                address = :address
                WHERE id = :id";

        return $this->db->prepare($sql)->execute([
            'id'      => $id,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'],
            'address' => $data['address']
        ]);
    }

    /**
     * Centralized Transaction Wrapper
     * Child classes will call this to ensure both tables are saved or both fail.
     */
    protected function executeTransaction(callable $callback)
    {
        try {
            $this->db->beginTransaction();
            $result = $callback();
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Partner Transaction Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a brand new partner record.
     * 
     * This handles the initial creation where a partner starts with a single role 
     * (e.g., just a customer OR just a supplier). The specified role column will 
     * be set to 1, while other role flags default to 0.
     */
    protected function createBasePartner($data, $roleColumn)
    {
        $stmt = $this->db->prepare("INSERT INTO partners (name, email, phone, address, {$roleColumn}) 
                                    VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['address']
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Activate an additional role for an existing partner.
     * 
     * @param int $id 
     * @param string $newRoleColumn 
     */
    protected function enableNewRole($id, $newRoleColumn)
    {
        $sql = "UPDATE partners SET {$newRoleColumn} = 1 WHERE id = :id";
        return $this->db->prepare($sql)->execute(['id' => $id]);
    }
}
