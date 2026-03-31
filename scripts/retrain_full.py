import json
from pathlib import Path
from app.vanna_setup import vn

TRAINING_FILE = Path(__file__).parent.parent / 'data' / 'training_data_full.json'


def load_training_payload():
    with open(TRAINING_FILE, 'r', encoding='utf-8') as f:
        return json.load(f)


def clear_existing_training():
    print('=== Menghapus semua training data lama ===')
    df = vn.get_training_data()
    for idx, row in df.iterrows():
        tid = row['id']
        if vn.remove_training_data(id=tid):
            print(f'Deleted: {tid}')
        else:
            print(f'Failed to delete: {tid}')
    print(f'Total dihapus: {len(df)}')


def train_schema_and_docs(payload):
    print('\n=== Training DDL untuk tabel-tabel dashboard ===')
    for table, ddl_text in payload['ddls'].items():
        print(f'Training DDL: {table}')
        vn.train(ddl=ddl_text)

    print('\n=== Training dokumentasi schema dan bisnis ===')
    for table, doc_text in payload['documentation'].items():
        print(f'Training documentation: {table}')
        vn.train(documentation=doc_text)


def train_sql_examples(payload):
    print('\n=== Training contoh SQL untuk pertanyaan dashboard ===')
    examples = payload.get('examples', [])
    for idx, item in enumerate(examples, start=1):
        question = item.get('question')
        sql = item.get('sql')
        if not question or not sql:
            continue
        vn.train(question=question, sql=sql)
        if idx % 50 == 0:
            print(f'Trained {idx} examples...')
    print(f'Total SQL examples trained: {len(examples)}')


def main():
    payload = load_training_payload()
    clear_existing_training()
    train_schema_and_docs(payload)
    train_sql_examples(payload)
    print('\n=== Selesai: semua data pelatihan sudah ditambahkan ===')


if __name__ == '__main__':
    main()
