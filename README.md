# Hectronic ChatBot

Flask web server for chatting with your MSSQL database using Vanna AI + Google Gemini.

## Features

- 🤖 **AI-Powered SQL Generation**: Natural language to SQL queries using Vanna AI and Google Gemini
- 💾 **Vector Database**: ChromaDB for efficient training data storage and retrieval
- 🔐 **Authentication**: Simple password-based authentication
- 📊 **Training Data Management**: Web interface to manage and update training data
- 🎨 **Modern UI**: Clean and responsive interface with gradient designs
- 🔄 **Real-time Chat**: Interactive chatbot interface for database queries
- 📈 **Query Results**: Display query results in formatted tables (limited to 5 rows by default)
- 📥 **Export to CSV**: Export query results to CSV file
- 🔍 **Search & Filter**: Advanced filtering for training data management
- 💬 **Chat Features**: Message history, typing indicators, suggestion chips
- 📋 **Copy SQL**: Copy generated SQL queries to clipboard
- 💾 **Chat History**: Save and load chat sessions per user with session management

## Project Structure

```
hec-bot/
├── app/
│   ├── __init__.py        # Flask app factory
│   ├── config.py          # Environment-based configuration
│   ├── vanna_setup.py     # Vanna AI + Gemini + ChromaDB init
│   ├── auth.py            # Simple password authentication
│   ├── cache.py           # In-memory cache
│   ├── chat_history.py    # Chat session storage and management
│   └── routes.py          # All API endpoints
├── data/                  # Runtime data and generated training files
│   ├── chromadb_data/     # ChromaDB vector storage
│   ├── chat_history.json  # User chat sessions
│   └── training_data_full.json
├── scripts/               # Utility and retraining scripts
│   ├── generate_training_data.py
│   └── retrain_full.py
├── static/                # Frontend assets
│   ├── assets/            # CSS and JS bundles
│   ├── index.html         # ChatBot interface
│   ├── training-data.html # Training data management
│   ├── sidebar.css        # Sidebar styles
│   └── *.png, *.svg       # Images and icons
├── references/            # Reference Laravel project (HecPoll 3)
├── .env                   # Environment variables (not committed)
├── .env.example           # Template for .env
├── app.py                 # Local dev entry point
├── wsgi.py                # Production WSGI entry point
├── LICENSE
├── README.md
└── requirements.txt
```

## Setup

### 1. Clone the repository
```bash
git clone <repository-url>
cd hec-bot
```

### 2. Create virtual environment
```bash
python -m venv venv

# Windows
venv\Scripts\activate

# Linux/Mac
source venv/bin/activate
```

### 3. Install dependencies
```bash
pip install -r requirements.txt
```

### 4. Configure environment variables
Copy `.env.example` to `.env` and fill in your credentials:
```bash
cp .env.example .env
```

Edit `.env` file:
```env
# Database Configuration
DB_HOST=your_database_host
DB_PORT=1433
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password

# Gemini API
GEMINI_API_KEY=your_gemini_api_key

# Authentication
ADMIN_PASSWORD=your_admin_password

# Flask Configuration
FLASK_SECRET_KEY=your_secret_key
FLASK_ENV=development
```

### 5. Generate training data (optional)
```bash
python scripts/generate_training_data.py
```

### 6. Train the model (optional)
```bash
python scripts/retrain_full.py
```

### 7. Run the application
```bash
# Development
python app.py

# Production with Gunicorn
pip install gunicorn
gunicorn wsgi:app --bind 0.0.0.0:5000 --workers 4
```

The application will be available at `http://localhost:5000`

## Usage

### Web Interface

1. **Login**: Navigate to `http://localhost:5000` and enter your admin password
2. **ChatBot**: Ask questions in natural language about your database
   - Example: "What is the total quantity per terminal?"
   - Example: "Show me all transactions from last week"
   - Click "Chat History" in sidebar to view/load previous conversations
   - Click "New Chat" to start a fresh conversation
   - All chats are automatically saved per user
3. **Training Data**: Manage training data at `http://localhost:5000/training-data`
   - Add new SQL examples
   - Add DDL statements
   - Add documentation
   - Edit or delete existing training data

### API Endpoints

#### Authentication
- `POST /api/v0/login` - Login with password
- `GET /api/v0/logout` - Logout

#### Chat
- `POST /api/v0/ask` - Ask a question
  ```json
  {
    "question": "What is the total quantity per terminal?"
  }
  ```

#### Chat History
- `GET /api/v0/chat/sessions` - Get all chat sessions for current user
- `POST /api/v0/chat/sessions` - Save/update a chat session
  ```json
  {
    "session_id": "optional-uuid",
    "title": "Chat title",
    "messages": [{"text": "...", "isUser": true, "data": {}, "time": "..."}]
  }
  ```
- `GET /api/v0/chat/sessions/<session_id>` - Get specific chat session
- `DELETE /api/v0/chat/sessions/<session_id>` - Delete a chat session
- `PUT /api/v0/chat/sessions/<session_id>/title` - Update session title
  ```json
  {
    "title": "New title"
  }
  ```

#### Training Data
- `GET /api/v0/get_training_data` - Get all training data
- `POST /api/v0/train` - Add new training data
  ```json
  {
    "question": "What is the total quantity?",
    "sql": "SELECT SUM(TransQuantity) FROM Transactions"
  }
  ```
  or
  ```json
  {
    "ddl": "CREATE TABLE ..."
  }
  ```
  or
  ```json
  {
    "documentation": "This table stores..."
  }
  ```
- `POST /api/v0/remove_training_data` - Remove training data
  ```json
  {
    "id": "training-data-id"
  }
  ```

#### Health Check
- `GET /api/v0/health` - Check API health status

## Technologies Used

- **Backend**: Flask (Python)
- **AI/ML**: Vanna AI, Google Gemini
- **Database**: MSSQL Server
- **Vector DB**: ChromaDB
- **Frontend**: Vanilla JavaScript, HTML5, CSS3
- **Icons**: Font Awesome
- **Fonts**: Inter (Google Fonts)

## Project Structure Details

### `/app` - Application Core
- `__init__.py`: Flask application factory and initialization
- `config.py`: Configuration management (dev/prod environments)
- `vanna_setup.py`: Vanna AI and ChromaDB setup
- `auth.py`: Authentication middleware
- `cache.py`: In-memory caching for query results
- `routes.py`: All API endpoints and route handlers

### `/data` - Runtime Data
- `chromadb_data/`: ChromaDB vector database storage
- `training_data_full.json`: Complete training data backup

### `/scripts` - Utility Scripts
- `generate_training_data.py`: Generate training data from database
- `retrain_full.py`: Retrain model with all training data

### `/static` - Frontend Assets
- `index.html`: ChatBot interface
- `training-data.html`: Training data management interface
- `sidebar.css`: Shared sidebar styles
- `assets/`: Compiled CSS and JS bundles

### `/references` - Reference Project
- Contains the Laravel HecPoll 3 project for reference

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

See LICENSE file for details.
