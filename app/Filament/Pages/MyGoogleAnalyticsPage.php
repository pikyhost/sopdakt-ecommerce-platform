<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BezhanSalleh\FilamentGoogleAnalytics\Widgets;
use Illuminate\Contracts\Support\Htmlable;

class MyGoogleAnalyticsPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-chart-bar';

    protected static string $view = 'filament.pages.my-google-analytics-page';

    protected static ?int $navigationSort = 3;

    public function getHeading(): string|Htmlable
    {
        return __('Google Analytics');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Analysis');
    }

    public static function getNavigationLabel(): string
    {
        return __('Google Analytics');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\PageViewsWidget::class,
            Widgets\VisitorsWidget::class,
            Widgets\ActiveUsersOneDayWidget::class,
            Widgets\ActiveUsersSevenDayWidget::class,
            Widgets\ActiveUsersTwentyEightDayWidget::class,
            Widgets\SessionsWidget::class,
            Widgets\SessionsDurationWidget::class,
            Widgets\SessionsByCountryWidget::class,
            Widgets\SessionsByDeviceWidget::class,
            Widgets\MostVisitedPagesWidget::class,
            Widgets\TopReferrersListWidget::class,
        ];
    }

/*

PageViewsWidget
عدد مرات مشاهدة الصفحات.
يُظهر إجمالي عدد المرات التي تم فيها عرض صفحات موقعك من قبل الزوار، سواء كانت نفس الصفحة أو صفحات مختلفة.

VisitorsWidget
عدد الزوار.
يُظهر عدد الأشخاص الفريدين الذين زاروا موقعك خلال فترة زمنية معينة، بغض النظر عن عدد الصفحات التي شاهدوها.

ActiveUsersOneDayWidget
المستخدمون النشطون خلال يوم واحد.
يُظهر عدد المستخدمين الذين تفاعلوا مع موقعك خلال آخر 24 ساعة.

ActiveUsersSevenDayWidget
المستخدمون النشطون خلال 7 أيام.
يُظهر عدد المستخدمين الذين استخدموا الموقع أو تفاعلوا معه خلال الأسبوع الماضي.

ActiveUsersTwentyEightDayWidget
المستخدمون النشطون خلال 28 يومًا.
يعطي نظرة طويلة الأمد حول عدد المستخدمين النشطين في آخر 4 أسابيع.

SessionsWidget
عدد الجلسات.
الجلسة تبدأ عند دخول المستخدم إلى الموقع وتنتهي بعد فترة من عدم النشاط أو عند مغادرته. هذا الودجت يعرض عدد تلك الجلسات.

SessionsDurationWidget
مدة الجلسات.
يُظهر متوسط الوقت الذي يقضيه المستخدم في الموقع خلال الجلسة الواحدة، مما يدل على مدى اهتمام الزائر بالمحتوى.

SessionsByCountryWidget
الجلسات حسب الدولة.
يُظهر من أي دول يأتي زوار موقعك وعدد الجلسات التي أُجريت من كل دولة.

SessionsByDeviceWidget
الجلسات حسب نوع الجهاز.
يُظهر ما إذا كان الزوار يستخدمون الهاتف المحمول، الحاسوب، أو الجهاز اللوحي أثناء تصفح الموقع.

MostVisitedPagesWidget
أكثر الصفحات زيارة.
يُظهر قائمة بأكثر صفحات موقعك زيارة، مما يساعدك على معرفة الصفحات الأكثر جاذبية للمستخدمين.

TopReferrersListWidget
أهم مصادر الإحالة.
يُظهر المواقع أو الروابط التي جاء منها الزوار إلى موقعك، مثل محركات البحث أو مواقع التواصل الاجتماعي.

لو تحب أشرح أي واحدة منهم بشكل أعمق أو أعطيك أمثلة، بلغني!

* */
}
