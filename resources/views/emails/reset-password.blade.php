<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - Boneh Star</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F8FAFC; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

    <!-- Wrapper Table -->
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; padding: 40px 16px;">
        <tr>
            <td align="center">
                <!-- Main Email Card -->
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; background-color: #FFFFFF; border-radius: 24px; border: 1px solid #E2E8F0; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03); overflow: hidden;">
                    <tr>
                        <td style="padding: 40px 36px;">
                            
                            <!-- Header Logo Section -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none;">
                                            <!-- Blue Star Logo Icon -->
                                            <svg width="42" height="42" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="vertical-align: middle;">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" fill="#0066FF"/>
                                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" stroke="#0066FF" stroke-width="1.5" stroke-linejoin="round"/>
                                            </svg>
                                            <div style="display: inline-block; text-align: left; vertical-align: middle; margin-left: 8px;">
                                                <span style="font-size: 20px; font-weight: 900; color: #0F172A; letter-spacing: -0.5px; display: block; line-height: 1;">BONEH<span style="color: #0066FF;">STAR</span></span>
                                                <span style="font-size: 9px; font-weight: 800; color: #64748B; letter-spacing: 2px; text-transform: uppercase; display: block; margin-top: 3px;">PARIWISATA</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <div style="height: 1px; background-color: #E2E8F0; margin: 32px 0 36px 0;"></div>

                            <!-- Greeting -->
                            <h1 style="margin: 0 0 20px 0; font-size: 24px; font-weight: 800; color: #0F172A; text-align: center; letter-spacing: -0.5px;">
                                Halo, <span style="color: #0066FF;">{{ $name }}</span>!
                            </h1>

                            <!-- Intro Paragraphs -->
                            <p style="margin: 0 0 12px 0; font-size: 15px; font-weight: 500; color: #475569; text-align: center; line-height: 1.6;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Boneh Star Anda.
                            </p>
                            <p style="margin: 0 0 32px 0; font-size: 15px; font-weight: 500; color: #475569; text-align: center; line-height: 1.6;">
                                Klik tombol di bawah ini untuk melanjutkan proses pengaturan ulang kata sandi.
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 32px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background-color: #0066FF; color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; padding: 14px 36px; border-radius: 12px; box-shadow: 0 4px 14px rgba(0, 102, 255, 0.25); text-align: center;">
                                            Atur Ulang Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiration Notice with Clock Icon -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 36px;">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                            <!-- Clock Icon SVG -->
                                            <div style="width: 28px; height: 28px; background-color: #EFF6FF; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; vertical-align: middle;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0066FF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle;">
                                                    <circle cx="12" cy="12" r="10"></circle>
                                                    <polyline points="12 6 12 12 16 14"></polyline>
                                                </svg>
                                            </div>
                                            <span style="font-size: 13px; font-weight: 600; color: #475569; vertical-align: middle; margin-left: 6px;">
                                                Tautan ini akan kedaluwarsa dalam <strong style="color: #0066FF;">60 menit</strong>.
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <div style="height: 1px; background-color: #F1F5F9; margin: 0 0 28px 0;"></div>

                            <!-- Security Warning Box -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #F8FAFC; border-radius: 16px; border: 1px solid #F1F5F9; margin-bottom: 36px;">
                                <tr>
                                    <td style="padding: 18px 20px;">
                                        <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="36" valign="top" style="padding-right: 12px;">
                                                    <!-- Shield Check SVG Icon -->
                                                    <div style="width: 32px; height: 32px; background-color: #EFF6FF; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0066FF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 6px; margin-left: 6px;">
                                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                                            <polyline points="9 12 11 14 15 10"></polyline>
                                                        </svg>
                                                    </div>
                                                </td>
                                                <td valign="middle" style="font-size: 13px; font-weight: 500; color: #64748B; line-height: 1.5;">
                                                    <strong style="color: #334155; font-weight: 700;">Jika Anda tidak merasa melakukan permintaan ini</strong>, abaikan email ini atau hubungi kami jika ada pertanyaan.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer Sign-off -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="font-size: 13px; font-weight: 500; color: #64748B;">
                                        Salam hangat,
                                        <div style="font-size: 14px; font-weight: 800; color: #0F172A; margin-top: 4px;">
                                            Tim Boneh Star
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
