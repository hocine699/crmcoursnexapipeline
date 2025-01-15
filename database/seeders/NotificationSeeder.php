<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NotificationTemplates;
use App\Models\NotificationTemplateLangs;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications = [
            'new_user' => 'New User', 'new_lead' => 'New Lead', 'new_task' => 'New Task', 'new_quotes' => 'New Quotes', 'new_salesorder' => 'New Sales Order',
            'new_invoice' => 'New Invoice', 'new_invoice_payment' => 'New Invoice Payment', 'new_meeting' => 'New Meeting'
        ];

        $defaultTemplate = [
            'new_user' => [
                'variables' => '{
                    "Email": "email",
                    "Password": "password",
                    "Company Name": "user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'تم تكوين مستخدم جديد بواسطة {user_name}',
                    'da' => 'Ny bruger oprettet af {bruger_navn}.',
                    'de' => 'Neuer Benutzer erstellt von {Benutzername}.',
                    'en' => 'New User created by {user_name}.',
                    'es' => 'Nueva usuario creada por {nombre_usuario}.',
                    'fr' => 'Nouvel utilisateur créé par {Nom_utilisateur}.',
                    'it' => 'Nuovo utente creato da {user_name}.',
                    'ja' => 'によって作成された新しいユーザー{ユーザー名}.',
                    'nl' => 'Nieuwe gebruiker gemaakt door {gebruikersnaam}.',
                    'pl' => 'Nowy użytkownik utworzony przez {nazwa_użytkownika}.',
                    'ru' => 'Новый пользователь, созданный {имя_пользователя}.',
                    'pt' => 'Novo Usuário criado por {user_name}.',
                    'tr' => '{user_name} tarafından oluşturulan Yeni Kullanıcı.',
                    'zh' => '由 {user_name} 创建的新用户。',
                    'he' => 'משתמש חדש נוצר על ידי {user_name}.',
                    'pt-br' => 'Novo usuário criado por {user_name}.',
                ],
            ],
            'new_lead' => [
                'variables' => '{
                    "Lead Name":"lead_name",
                    "Lead Email": "lead_email",
                    "Company Name":"company_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'جديد تم تكوينه بواسطة {ليللي_name}.',
                    'da' => 'Ny ledelse oprettet af {lead_navn}.',
                    'de' => 'Neuer Lead erstellt von {Lead_name}.',
                    'en' => 'New Lead created by {lead_name}.',
                    'es' => 'Nuevo cliente potencial creado por {nombre_tabla}.',
                    'fr' => 'Nouvelle opportunité créée par {Nom_chef}.',
                    'it' => 'Nuovo Lead creato da {lead_name}.',
                    'ja' => '新規リードの作成者 {先頭名}.',
                    'nl' => 'Nieuwe leider gemaakt door {naam_item}.',
                    'pl' => 'Nowy kierownik utworzony przez {nazwa_przywódcy}.',
                    'ru' => 'Новый руководитель, созданный {имя_руководства}.',
                    'pt' => 'Novo Lead criado por {lead_name}.',
                    'tr' => '{lead_name} tarafından oluşturulan yeni Potansiyel Müşteri.',
                    'zh' => '由 {lead_name} 创建的新商机。',
                    'he' => 'ביצוע חדש שנוצר על-ידי {lead_name}.',
                    'pt-br' => 'Novo Lead criado por {lead_name}.',
                ],
            ],
            'new_task' => [
                'variables' => '{
                    "Task Name":"task_name",
                    "Task Start Date": "task_start_date",
                    "Task Due Date":"task_due_date",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'مهمة جديدة {اسم المهمة} تكوين بواسطة {user_name}',
                    'da' => 'Ny opgave {opgaveravn} oprettet af {bruger_navn}',
                    'de' => 'Neue Aufgabe {Taskname} erstellt von {Benutzername}',
                    'en' => 'New Task {task_name} created by {user_name}',
                    'es' => 'Nueva tarea {nombre_tarea} creado por {nombre_usuario}',
                    'fr' => 'Nouvelle tâche {Nom_tâche} Créé par {Nom_utilisateur}',
                    'it' => 'Nuova attività {task_name} creato da {user_name}',
                    'ja' => '新規タスク {タスク名名} 作成者 {ユーザー名}',
                    'nl' => 'Nieuwe taak {taaknaam} gemaakt door {gebruikersnaam}',
                    'pl' => 'Nowe zadanie {nazwa_zadania} utworzone przez {nazwa_użytkownika}',
                    'ru' => 'Создать задачу {имя_задачи} кем создано {имя_пользователя}',
                    'pt' => 'Nova Tarefa {task_name} criado por {user_name}',
                    'tr' => '{user_name} tarafından oluşturulan {task_name} adlı yeni Görev',
                    'zh' => '{user_name} 创建的新任务 {task_name}',
                    'he' => 'משימה חדשה {task_name} נוצרה על-ידי {user_name}',
                    'pt-br' => 'Nova tarefa {task_name} criada por {user_name}',
                ],
            ],
            'new_quotes' => [
                'variables' => '{
                    "Quote Number":"quote_number",
                    "Billing Address": "billing_address",
                    "Shipping Address":"shipping_address",
                    "Quoted Date":"date_quoted",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'تم تكوين علامة تنصيص جديدة بواسطة {user_name}.',
                    'da' => 'Nyt tilbud oprettet af {bruger_navn}.',
                    'de' => 'Neues Angebot erstellt von {Benutzername}.',
                    'en' => 'New Quote created by {user_name}.',
                    'es' => 'Nuevo presupuesto creado por {nombre_usuario}.',
                    'fr' => 'Nouveau devis créé par {Nom_utilisateur}.',
                    'it' => 'Nuovo Preventivo creato da {user_name}.',
                    'ja' => '新規見積作成者 {ユーザー名}.',
                    'nl' => 'Nieuwe prijsopgave gemaakt door {gebruikersnaam}.',
                    'pl' => 'Nowa oferta utworzona przez {nazwa_użytkownika}.',
                    'ru' => 'Создать расценку, созданную {имя_пользователя}.',
                    'pt' => 'Nova Cotação criada por {user_name}.',
                    'tr' => '{user_name} tarafından oluşturulan yeni Fiyat Teklifi.',
                    'zh' => '{ user_name} 创建的新报价。',
                    'he' => 'הצעת מחיר חדשה שנוצרה על-ידי {user_name}.',
                    'pt-br' => 'Nova Citação criada por {user_name}.',
                ],
            ],
            'new_salesorder' => [
                'variables' => '{
                    "Quote Number":"quote_number",
                    "Billing Address": "billing_address",
                    "Shipping Address":"shipping_address",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'نظام سلطة جديد {رقم _ quote_number}  تكوين بواسطة {user_name}.',
                    'da' => 'Ny Salesorder {cik_nummer} oprettet af {bruger_navn}.',
                    'de' => 'Neuer Salesorder {quote_nummer} erstellt von {Benutzername}.',
                    'en' => 'New Salesorder {quote_number} created by {user_name}.',
                    'es' => 'New Salesorder {número_cantidad} creado por {nombre_usuario}.',
                    'fr' => 'Nouvelle commande Salesorder {Numéro_quota} Créé par {Nom_utilisateur}.',
                    'it' => 'Nuovo Salesorder {quote_numero} creato da {user_name}.',
                    'ja' => '新規販売オーダー {quote_number} 作成者 {ユーザー名}.',
                    'nl' => 'Nieuwe verkooporder {quote_number} gemaakt door {gebruikersnaam}.',
                    'pl' => 'Nowy porządek Salesorder {quote_number} utworzone przez {nazwa_użytkownika}.',
                    'ru' => 'Новый заказ на продажу {quote_число} кем создано {имя_пользователя}.',
                    'pt' => 'Novo Vendedor {quote_número} criado por {user_name}.',
                    'tr' => '{user_name} tarafından oluşturulan {quote_number} adlı yeni Satış Siparişi.',
                    'zh' => '{ user_name} 已创建新的 Salesorder {quote_number} 。',
                    'he' => 'הזמנת Salesorder חדשה {quote_number} שנוצרה על-ידי {user_name}.',
                    'pt-br' => 'Novo Salesorder {quote_number} criado por {user_name}.',
                ],
            ],
            'new_invoice' => [
                'variables' => '{
                    "Invoice Number":"invoice_id",
                    "Invoice Sub Total":"invoice_sub_total",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'تم تكوين فاتورة جديدة { invoice_id } بواسطة  {user_name}.',
                    'da' => 'Ny faktura { invoice_id } oprettet af  {bruger_navn}.',
                    'de' => 'Neue Rechnung {invoice_id} erstellt von  {Benutzername}.',
                    'en' => 'New invoice {invoice_id} created by {user_name}.',
                    'es' => 'Nueva factura {invoice_id} creada por  {nombre_usuario}.',
                    'fr' => 'Nouvelle facture { invoice_id } créée par  {Nom_utilisateur}.',
                    'it' => 'Nuova fattura {invoice_id} creata da  {user_name}.',
                    'ja' => '新規請求書 {invoice_id} が作成されました  {ユーザー名}.',
                    'nl' => 'Nieuwe factuur { invoice_id } gemaakt door  {gebruikersnaam}.',
                    'pl' => 'Nowa faktura {invoice_id } utworzona przez  {nazwa_użytkownika}.',
                    'ru' => 'Новый инвойс { invoice_id } создан  {имя_пользователя}.',
                    'pt' => 'Nova fatura {invoice_id} criada por  {user_name}.',
                    'tr' => '{user_name} tarafından oluşturulan yeni fatura {invoice_id}.',
                    'zh' => '{user_name} 创建了新的发票 {invoice_id} 。',
                    'he' => 'חשבונית חדשה {invoice_id} נוצרה על-ידי {user_name}.',
                    'pt-br' => 'Nova fatura {invoice_id} criada por {user_name}.',
                ],
            ],
            'new_invoice_payment' => [
                'variables' => '{
                    "user":"name",
                    "amount":"amount",
                    "payment_type" :"payment_type",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'تم تكوين دفعة جديدة من { كمية } بالنسبة الى { user_name } بواسطة { payment_type }.',
                    'da' => 'Ny betaling af { amount } oprettet for { user_name } af { payment_type }.',
                    'de' => 'Neue Zahlung von {amount} erstellt für {user_name} von {payment_type}.',
                    'en' => 'New payment of {amount} created for {user_name} by {payment_type}.',
                    'es' => 'Nuevo pago de {amount} creado para {user_name} por {payment_type}.',
                    'fr' => 'Nouveau paiement de { amount } créé pour { user_name } par { payment_type }.',
                    'it' => 'Nuovo pagamento di {importo} creato per {user_name} da {payment_type}.',
                    'ja' => '{ payment_type} によって { user_name} に対して作成された {金額} の新規支払いが発生しました。',
                    'nl' => 'Nieuwe betaling van { amount } gemaakt voor { user_name } door { payment_type }.',
                    'pl' => 'Nowa płatność {amount } została utworzona dla użytkownika {user_name } przez użytkownika {payment_type }.',
                    'ru' => 'Новая оплата { сумма }, созданная для { имя_пользователя }, { payment_type }.',
                    'pt' => 'Novo pagamento de {amount} criado para {user_name} por {payment_type}.',
                    'tr' => '{payment_type} tarafından {user_name} için {amount} tutarında yeni ödeme oluşturuldu.',
                    'zh' => '{payment_type}为 {user_name} 创建了新支付 { 金额} 。',
                    'he' => 'תשלום חדש של {לסכום} שנוצר עבור {user_name} על-ידי {payment_type}.',
                    'pt-br' => 'Novo pagamento de {valor} criado para {user_name} por {payment_type}.',
                ],
            ],
            'new_meeting' => [
                'variables' => '{
                    "Meeting Title":"meeting_name",
                    "Meeting Start Date":"meeting_start_date",
                    "Meeting Due Date":"meeting_due_date",
                    "Company Name":"user_name",
                    "App Name": "app_name"
                    }',
                'lang' => [
                    'ar' => 'تم تكوين اجتماع جديد { atteming_name } بواسطة {user_name}.',
                    'da' => 'Nyt møde { meeting_name } oprettet af {bruger_navn}.',
                    'de' => 'Neue Besprechung {meeting_name} erstellt von {Benutzername}.',
                    'en' => 'New Meeting {meeting_name} created by {user_name}.',
                    'es' => 'Se ha creado la nueva reunión {meeting_name} {nombre_usuario}.',
                    'fr' => 'Nouvelle réunion { meeting_name } créée par {Nom_utilisateur}.',
                    'it' => 'Nuova riunione {meeting_name} creata da {user_name}.',
                    'ja' => '新規ミーティング {meeting_name} が作成されました {ユーザー名}.',
                    'nl' => 'Nieuwe vergadering { meeting_naam } gemaakt door {gebruikersnaam}.',
                    'pl' => 'Nowe spotkanie {meeting_name } utworzone przez {nazwa_użytkownika}.',
                    'ru' => 'Создано новое собрание { meeting_name }, созданное {имя_пользователя}.',
                    'pt' => 'Novo Meeting {meeting_name} criado por {user_name}.',
                    'tr' => '{user_name} tarafından oluşturulan {meeting_name} adlı yeni Toplantı.',
                    'zh' => '{user_name} 已创建新的会议 {meeting_name} 。',
                    'he' => 'פגישה חדשה {meeting_name} נוצרה על-ידי {user_name}.',
                    'pt-br' => 'Nova reunião {meeting_name} criada por {user_name}.',
                ],
            ],
        ];
        $user = User::where('type', 'super admin')->first();

        foreach ($notifications as $k => $n) {
            $ntfy = NotificationTemplates::where('slug', $k)->count();
            if ($ntfy == 0) {
                $new = new NotificationTemplates();
                $new->name = $n;
                $new->slug = $k;
                $new->save();

                foreach ($defaultTemplate[$k]['lang'] as $lang => $content) {
                    NotificationTemplateLangs::create(
                        [
                            'parent_id' => $new->id,
                            'lang' => $lang,
                            'variables' => $defaultTemplate[$k]['variables'],
                            'content' => $content,
                            'created_by' =>  1,
                        ]
                    );
                }
            }
        }
    }
}
