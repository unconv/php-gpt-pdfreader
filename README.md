# PHP-GPT-PDFReader

Use the ChatGPT API to answer questions about content in a PDF file without using OpenAI embeddings API.

## Usage

```console
$ php conversation.php
```

You can `export OPENAI_API_KEY=[YOUR_API_KEY]` if you don't want to input it every time.

## Requirements

The PDF to text conversion requires `pdftotext`:

```console
$ sudo apt install poppler-utils
```
