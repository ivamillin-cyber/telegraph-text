<?php
//строгие типы
declare(strict_types=1);
//объявляю класс, помещаю свойства
class TelegraphText
{
    public string $title;
    public string $text;
    public string $author;
    public string $published;
    public string $slug;
    //константа из пункта 4 задания
    private const EXTENSION = '.txt';
    //создается объект; str_replace функция замены; $title беру значение параметра и кладу в slug; date функция даты
    public function __construct(string $title, string $author, string $text)
    {
        $this->title = $title;
        $this->author = $author;
        $this->text = $text;
        $this->published = date('Y-m-d H:i:s');
        $this->slug = str_replace(' ', '-', $title);
    }
    //void чтобы php не требовал вернуть строку
    public function editText(string $title, string $text): void
    { //меняю только заголовок и текст по заданию
        $this->title = $title;
        $this->text = $text;
    }
    //параметров нет; метод вернет строку
    public function storeText(): string
    {  // создала ассоциативный массив; slug не положила тк он как имя файла используется
        $data = array(
            'text' => $this->text,
            'title' => $this->title,
            'author' => $this->author,
            'published' => $this->published,
        );
        // функция открывает файл,пишет туда строку и закрывает файл
        //также может создать файл и перезаписать.
        //$this->slug . self::EXTENSION — склеивание двух строк в одну
        //точка склеивает
        //self::EXTENSION — обращение к константе
        //self как бы указывает этот класс
        //итог в правке:вместо new-title . .txt - new-title.txt
        file_put_contents($this->slug . self::EXTENSION, serialize($data));
    //вернуть результат из метода как и было обещано
        return $this->slug;
    }
    //static такой метод вызывается у класса а не у объекта
    public static function loadText(string $slug): ?TelegraphText
    {    //собираю полное имя файла
        $filename = $slug . self::EXTENSION;
        //проверка существует ли файл
        if (!file_exists($filename)) {
            return null;
        }else {                                
            $content = file_get_contents($filename);
        }
         //функция читает весь файл и возвращает его содержимое как строку
        $content = file_get_contents($filename);
        //пусто ли
        if (empty($content)) {
            return null;
        }
        //обратно из строки в массив,распаковка
        $data = unserialize($content);

        //создаю новый объект этого класса,передаю 3 параметра
        //из массива беру значения через $data.квадратные скобки со строковым ключем это доступ к элементу ассоциативного массива
        $text = new TelegraphText($data['title'], $data['author'], $data['text']);
        //перезаписываю published
        $text->published = $data['published'];

        return $text;
    }
}

// показываю что класс работает

$note = new TelegraphText('title', 'Милана', 'что-то написала');
$note->editText('new title', 'еще текст');
$slug = $note->storeText();;

$loaded = TelegraphText::loadText($slug);
print $loaded->title . PHP_EOL; 

$loaded->editText('Обновлённый заголовок', 'и еще текст');
$slug = $loaded->storeText();

echo TelegraphText::loadText($slug)->title . PHP_EOL;