# Scripts Directory

This directory contains utility scripts for managing training data and retraining the Vanna AI model.

## Available Scripts

### generate_training_data.py
Generates training data from the MSSQL database schema and sample queries.

**Usage:**
```bash
python scripts/generate_training_data.py
```

**What it does:**
- Connects to the MSSQL database
- Extracts table schemas (DDL)
- Generates sample SQL queries
- Creates documentation for tables and columns
- Saves output to `data/training_data_full.json`

### retrain_full.py
Retrains the Vanna AI model with all training data from the database.

**Usage:**
```bash
python scripts/retrain_full.py
```

**What it does:**
- Loads training data from `data/training_data_full.json`
- Clears existing ChromaDB data
- Trains the model with:
  - DDL statements (table schemas)
  - SQL queries with questions
  - Documentation strings
- Updates the ChromaDB vector database

## Notes

- Make sure `.env` file is configured with correct database credentials before running scripts
- Training data is stored in `data/training_data_full.json`
- ChromaDB vector database is stored in `data/chromadb_data/`
- Retraining will clear all existing training data in ChromaDB

## Environment Variables Required

```
DB_HOST=your_database_host
DB_PORT=1433
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password
GEMINI_API_KEY=your_gemini_api_key
```
