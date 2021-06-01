<?php
class Statistic_model extends CI_Model{

    function girls()
    {
        $this->db->select('COUNT(events.id) AS count_visits, element_id as girl_id, image_id, url_image, url_thumbnail');
        $this->db->where('events.type_id', 52);
        $this->db->where('events.created_at >=', date('Y-m-d', strtotime(date('Y-m-d H:i:s'). ' - 28 days')));
        $this->db->group_by('element_id, image_id, url_image, url_thumbnail');
        $this->db->join('users', 'events.element_id = users.id');
        $this->db->order_by('COUNT(events.id)', 'desc');
        
        $girls = $this->db->get('events');

        return $girls;
    }

    function albums()
    {
        $this->db->select('COUNT(events.id) AS count_visits, element_id as album_id, image_id, title, posts.related_1 AS girl_id');
        $this->db->where('type_id', 51);
        $this->db->where('events.created_at >=', date('Y-m-d', strtotime(date('Y-m-d H:i:s'). ' - 28 days')));
        $this->db->group_by('element_id, image_id, title, posts.related_1');
        $this->db->join('posts', 'events.element_id = post.id');
        $this->db->order_by('COUNT(events.id)', 'desc');
        
        $albums = $this->db->get('events');

        return $albums;
    }
}