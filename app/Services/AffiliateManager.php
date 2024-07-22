<?php

namespace App\Services;

use App\Models\Affiliate;
use Auth;
use DB;
use Notifications;

class AffiliateManager extends Service {
    /**
     * Create an affiliation request.
     *
     * @param mixed $data
     * @param mixed $user
     * @param mixed $slug
     */
    public function createAffiliate($data, $user, $slug) {
        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $user = Auth::user()->id;
            }

            $saveData = [
                'name'            => $data['name'],
                'url'             => $data['url'],
                'staff_id'        => null,
                'staff_commments' => null,
                'image_url'       => $data['image_url'] ?? null,
                'description'     => $data['description'] ?? null,
                'message'         => $data['message'] ?? null,
                'guest_name'      => $data['guest_name'] ?? null,
                'user_id'         => $user ? $user : null,
                'slug'            => $slug,
            ];
            $affiliate = Affiliate::create($saveData);

            if ($user) {
                Notifications::create('AFFILIATE_PENDING', Auth::user(), [
                    'affiliate_name' => $affiliate->name,
                    'affiliate_slug' => $affiliate->slug,
                ]);
            }

            return $this->commitReturn($affiliate);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
