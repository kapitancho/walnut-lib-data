# OpenApi v.3 compatible data importer
Attribute based data importer and validator

## Examples

### Book
```php
final readonly class BookData {
    public function __construct(
        #[IntegerData(minimum: 1, maximum: 999999)]
        public int $numPages,
        
        #[StringData(minLength: 1, maxLength: 100)]
        public string $authorName,
        
        #[IntegerData(minimum: 1, maximum: 9999)]
        public int $issueYear,
        
        #[StringData(minLength: 1, maxLength: 100)]
        public string $publisherName,
        
        #[StringData(minLength: 1, maxLength: 30)]
        public string $language
    ) {}
}
```

```php
$bookData = [
    'numPages' => 10,
    'authorName' => 'John Lock',
    'issueYear' => 2003,
    'publisherName' => "O'Really?",
    'language' => 'English'
];
$bookObject = $importer->import($bookData, BookData::class);
$importer->validate($bookObject); //ok
```