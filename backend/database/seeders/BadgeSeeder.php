<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => '初来乍到',
                'code' => 'newcomer',
                'icon' => '🎯',
                'description' => '欢迎加入社区，开启您的邻里之旅',
                'source_type' => Badge::SOURCE_REGISTER,
                'source_value' => 1,
                'sort' => 1,
            ],
            [
                'name' => '热心肠',
                'code' => 'helpful',
                'icon' => '💝',
                'description' => '热心回答邻里问题，是社区的好帮手',
                'source_type' => Badge::SOURCE_ANSWERS,
                'source_value' => 10,
                'sort' => 2,
            ],
            [
                'name' => '智多星',
                'code' => 'wise',
                'icon' => '⭐',
                'description' => '您的建议多次被采纳，是社区的智囊',
                'source_type' => Badge::SOURCE_ADOPTS,
                'source_value' => 5,
                'sort' => 3,
            ],
            [
                'name' => '公益达人',
                'code' => 'charity_lover',
                'icon' => '❤️',
                'description' => '积极参与公益活动，传递正能量',
                'source_type' => Badge::SOURCE_CHARITY,
                'source_value' => 5,
                'sort' => 4,
            ],
            [
                'name' => '活跃分子',
                'code' => 'active',
                'icon' => '🔥',
                'description' => '经常在社区发帖分享，活跃度满满',
                'source_type' => Badge::SOURCE_TOPICS,
                'source_value' => 50,
                'sort' => 5,
            ],
            [
                'name' => '金口碑',
                'code' => 'gold_reputation',
                'icon' => '🏆',
                'description' => '累计积分达到100分，社区认可您的贡献',
                'source_type' => Badge::SOURCE_POINTS,
                'source_value' => 100,
                'sort' => 6,
            ],
            [
                'name' => '常青树',
                'code' => 'evergreen',
                'icon' => '🌲',
                'description' => '注册满一年，感谢您长期以来的陪伴',
                'source_type' => Badge::SOURCE_DURATION,
                'source_value' => 365,
                'sort' => 7,
            ],
            [
                'name' => '社区之星',
                'code' => 'community_star',
                'icon' => '🌟',
                'description' => '累计积分达到1000分，社区的闪耀之星',
                'source_type' => Badge::SOURCE_POINTS,
                'source_value' => 1000,
                'sort' => 8,
            ],
            [
                'name' => '社区领袖',
                'code' => 'community_leader',
                'icon' => '👑',
                'description' => '累计积分达到2000分，社区的领军人物',
                'source_type' => Badge::SOURCE_POINTS,
                'source_value' => 2000,
                'sort' => 9,
            ],
            [
                'name' => '社区元老',
                'code' => 'community_elder',
                'icon' => '🏛️',
                'description' => '累计积分达到5000分，社区的传奇人物',
                'source_type' => Badge::SOURCE_POINTS,
                'source_value' => 5000,
                'sort' => 10,
            ],
            [
                'name' => '答疑高手',
                'code' => 'qa_master',
                'icon' => '🎓',
                'description' => '累计回答50个问题，是邻里信赖的顾问',
                'source_type' => Badge::SOURCE_ANSWERS,
                'source_value' => 50,
                'sort' => 11,
            ],
            [
                'name' => '最佳顾问',
                'code' => 'best_advisor',
                'icon' => '💎',
                'description' => '累计20个答案被采纳，专业度令人钦佩',
                'source_type' => Badge::SOURCE_ADOPTS,
                'source_value' => 20,
                'sort' => 12,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['code' => $badge['code']],
                $badge
            );
        }
    }
}
