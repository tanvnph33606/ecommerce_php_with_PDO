<?php
class OrderModel extends BaseModel
{
    use SweetAlert;
    public function tableName()
    {
        return 'orders';
    }
    public function tableField()
    {
        return '*';
    }
    public function primaryKey()
    {
        return 'id';
    }
    function getAllOrderItemByUser($user_id, $order_id)
    {
        $sql = "
            SELECT 
                o.id AS order_id, o.order_code, o.fullname, o.phone, o.address, o.order_date, o.order_status_id, o.total_money, o.coupon_id,
                pd.display_name AS payment_method_name,
                os.name AS order_status_name,
                oi.quantity, oi.price, oi.product_variant_id,
                av.value_name AS attribute_value,
                prd.title, prd.thumb, prd.id AS prod_id, prd.slug
            FROM 
                orders o
            INNER JOIN 
                payment p ON p.order_id = o.id
            INNER JOIN 
                payment_method pd ON pd.id = p.payment_method_id
            INNER JOIN 
                order_status os ON os.id = o.order_status_id
            INNER JOIN 
                order_item oi ON oi.order_id = o.id
            INNER JOIN 
                product_variants pv ON pv.id = oi.product_variant_id
            INNER JOIN 
                variants_value vv ON pv.id = vv.product_variant_id
            INNER JOIN 
                attribute a ON vv.attribute_id = a.id
            INNER JOIN 
                attribute_value av ON vv.attribute_value_id = av.id
            INNER JOIN 
                product prd ON prd.id = pv.prod_id
            WHERE 
                o.user_id = $user_id AND o.id = $order_id;

        ";

        $data = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    function getAllOrderByUser($user_id)
    {
        return $this->db->table('orders o')->select('o.id AS order_id, o.order_code, o.total_money, o.order_date, pd.display_name AS payment_method_name, os.name AS order_status_name, o.order_status_id')->join('payment p', 'p.order_id = o.id')->join('payment_method pd', 'pd.id = p.payment_method_id')->join('order_status os', 'os.id = o.order_status_id')->where('o.user_id', '=', $user_id)->get();
    }


    function getAllOrder()
    {
        return $this->db->table('orders o')->select('o.id AS order_id, o.user_id, o.order_code, o.total_money, o.order_date, pd.display_name AS payment_method_name, os.name AS order_status_name, o.order_status_id, u.fullname')->join('payment p', 'p.order_id = o.id')->join('payment_method pd', 'pd.id = p.payment_method_id')->join('user u', 'o.user_id = u.id')->join('order_status os', 'os.id = o.order_status_id')->get();
    }

    function addNewOrder($data)
    {
        // ->select('o.id, o.order_code, o.total_money, os.id AS order_status_id, os.name, o.order_date, pd.display_name')
        return $this->db->create($this->tableName(), $data);
    }

    function addNewOrderItem($data)
    {
        return $this->db->create('order_item', $data);
    }

    function updateOrder($id, $data)
    {
        return $this->db->findByIdAndUpdate($this->tableName(), $id, $data);
    }
}
