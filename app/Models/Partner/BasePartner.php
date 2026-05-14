<?php
abstract class BasePartner
{
    protected $db;
    protected $table;      // Defined in child (e.g., 'customers')
    protected $childFields = []; // Defined in child (e.g., ['credit_limit', 'customer_level'])

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get paginated list of partners with joined child data and dynamic filters
     * Child controllers will call this method, passing in their specific SQL parts and filter configs.
     */
    protected function getPaginatedPartners($selectSql, $fromWhereSql, $extraSearchColumns = [], $extraFiltersConfig = [], $allowedSort = [])
    {
        $conditions = ["p.deleted_at IS NULL"];

        $filtersConfig = [
            'type' => ['col' => 'p.type', 'type' => 'string'],
        ];

        // Merge with child-specific filters defined in the controller
        $filtersConfig = array_merge($filtersConfig, $extraFiltersConfig);
        $params = [];
        foreach ($filtersConfig as $key => $val) {
            if (isset($_GET[$key]) && $_GET[$key] !== '') {
                $op = $val['op'] ?? '=';
                $paramName = "filter_" . $key;
                $conditions[] = "{$val['col']} {$op} :{$paramName}";
                if ($val['type'] === 'int') $params[$paramName] = (int)$_GET[$key];
                elseif ($val['type'] === 'float') $params[$paramName] = (float)$_GET[$key];
                else $params[$paramName] = $_GET[$key];
            }
        }

        $fullFromWhere = $fromWhereSql . " WHERE " . implode(" AND ", $conditions);

        $searchColumns = array_merge(['p.name', 'p.email', 'p.phone', 'p.address'], $extraSearchColumns); // Allow searching by partner and child fields

        require_once '../config/functions/pagination_helper.php';
        return getPaginatedData($this->db, $selectSql, $fullFromWhere, $searchColumns, $allowedSort, 'p.id', 'ASC', $params);
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
                address = :address,
                type = :type
                WHERE id = :id";

        return $this->db->prepare($sql)->execute([
            'id'      => $id,
            'name'    => $data['name'],
            'email'   => $data['email'],
            'phone'   => $data['phone'],
            'address' => $data['address'],
            'type'    => $data['type']
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
}
