<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFC Olvasó</title>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const log = (message) => {
                console.log(message);
                document.getElementById("log").innerText += message + "\n";
            };

            const scanButton = document.getElementById("scanButton");

            scanButton.addEventListener("click", async () => {
                log("Felhasználó rákattintott a szkennelés gombra");

                try {
                    const ndef = new NDEFReader();
                    await ndef.scan();
                    log("> Az NFC szkennelés elkezdődött");

                    ndef.addEventListener("readingerror", () => {
                        log("Hiba történt! Nem sikerült adatot olvasni a NFC tag-ból.");
                    });

                    ndef.addEventListener("reading", async ({ message, serialNumber }) => {
                        log(`> Sorozatszám: ${serialNumber}`);
                        log(`> Rekordok száma: ${message.records.length}`);

                        // FormData objektum létrehozása
                        const formData = new FormData();
                        formData.append("tagNumber", serialNumber);

                        // Loggoljuk a küldött adatot
                        log("Küldött adat: " + serialNumber);

                        // Az NFC tag számának elküldése a PHP szervernek
                        const response = await fetch('192.168.1.207/pdf/process_nfc.php', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await response.json();
                        log(`> Válasz a szervertől: ${data.message}`);
                    });
                } catch (error) {
                    log("Hiba: " + error);
                }
            });
        });
    </script>
</head>
<body>
    <h1>NFC Olvasó</h1>
    <button id="scanButton">Szkennelés</button>
    <pre id="log"></pre>
</body>
</html>
