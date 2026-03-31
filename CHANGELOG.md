# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Training Data management interface with search and filter capabilities
- Bulk delete functionality for training data
- Real-time health check status indicator
- Toast notifications for user feedback
- Pagination for training data table (20 items per page)
- Type filter dropdown (SQL, DDL, Documentation)
- Modern gradient UI design matching HecPoll 3 style
- **ChatBot interface with modern features:**
  - Message bubbles with avatars
  - Typing indicator
  - Suggestion chips for quick start
  - Auto-resize textarea
  - Copy SQL to clipboard
  - **Export query results to CSV**
  - **Table result limiting (5 rows by default)**
  - **Show All button for full results**
  - Result count display
  - Clear chat history
  - Enter to send, Shift+Enter for new line

### Changed
- Restructured project layout with dedicated `data/` and `scripts/` directories
- Updated training data interface layout to match transactions page
- Improved search functionality with real-time filtering
- Enhanced modal designs with gradient headers
- Optimized table height for better viewport usage

### Fixed
- Header and search bar spacing alignment
- Filter bar spacing consistency
- Table responsive design issues
- Modal z-index conflicts

### Removed
- Temporary fix scripts (fix_remaining_queries.py, fix_spacing.py, etc.)
- Duplicate training_data_full.json from scripts directory
- Unnecessary documentation files (GENERATE_TRAINING_DATA_FIX.md, etc.)

## [1.0.0] - Initial Release

### Added
- Flask web server with Vanna AI integration
- Google Gemini API integration for SQL generation
- ChromaDB vector database for training data storage
- Simple password authentication
- ChatBot interface for natural language queries
- Training data management system
- MSSQL database connection
- In-memory caching for query results
- Health check endpoint
- Responsive sidebar navigation
- Dark mode compatible UI

### Features
- Natural language to SQL conversion
- Interactive chat interface
- Training data CRUD operations
- Real-time query execution
- Result visualization in tables
- Session management
- Error handling and logging
