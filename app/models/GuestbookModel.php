<?php
class GuestbookModel extends Model {
    private $dataFile = 'messages.inc';

    public function saveMessage($data) {
        $date = date('d.m.y');
        $line = implode(';', [
                $date,
                htmlspecialchars(trim($data['last_name'])),
                htmlspecialchars(trim($data['first_name'])),
                htmlspecialchars(trim($data['patronymic'])),
                htmlspecialchars(trim($data['email'])),
                htmlspecialchars(trim($data['message']))
            ]) . PHP_EOL;

        // Добавляем в начало файла, чтобы новые сообщения были сверху
        if (file_exists($this->dataFile)) {
            $currentContent = file_get_contents($this->dataFile);
            file_put_contents($this->dataFile, $line . $currentContent);
        } else {
            file_put_contents($this->dataFile, $line);
        }

        return true;
    }

    public function getMessages() {
        $messages = [];

        if (file_exists($this->dataFile)) {
            $lines = file($this->dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $parts = explode(';', $line, 6);

                if (count($parts) == 6) {
                    $messages[] = [
                        'date' => $parts[0],
                        'last_name' => $parts[1],
                        'first_name' => $parts[2],
                        'patronymic' => $parts[3],
                        'email' => $parts[4],
                        'message' => $parts[5]
                    ];
                }
            }
        }

        return $messages;
    }
}
?>