import os
import json
from datetime import datetime
from typing import List, Dict, Optional

class ChatHistory:
    def __init__(self, data_dir: str = "data"):
        self.data_dir = data_dir
        self.history_file = os.path.join(data_dir, "chat_history.json")
        self._ensure_file_exists()
    
    def _ensure_file_exists(self):
        os.makedirs(self.data_dir, exist_ok=True)
        if not os.path.exists(self.history_file):
            with open(self.history_file, 'w') as f:
                json.dump({}, f)
    
    def _load_all(self) -> Dict:
        with open(self.history_file, 'r') as f:
            return json.load(f)
    
    def _save_all(self, data: Dict):
        with open(self.history_file, 'w') as f:
            json.dump(data, f, indent=2)
    
    def save_session(self, user_email: str, session_id: str, title: str, messages: List[Dict]) -> bool:
        try:
            data = self._load_all()
            if user_email not in data:
                data[user_email] = {}
            
            data[user_email][session_id] = {
                "title": title,
                "messages": messages,
                "created_at": datetime.now().isoformat(),
                "updated_at": datetime.now().isoformat()
            }
            
            self._save_all(data)
            return True
        except Exception as e:
            print(f"Error saving session: {e}")
            return False
    
    def get_user_sessions(self, user_email: str) -> List[Dict]:
        try:
            data = self._load_all()
            user_data = data.get(user_email, {})
            
            sessions = []
            for session_id, session_data in user_data.items():
                sessions.append({
                    "id": session_id,
                    "title": session_data.get("title", "Untitled Chat"),
                    "created_at": session_data.get("created_at"),
                    "updated_at": session_data.get("updated_at"),
                    "message_count": len(session_data.get("messages", []))
                })
            
            # Sort by updated_at descending
            sessions.sort(key=lambda x: x.get("updated_at", ""), reverse=True)
            return sessions
        except Exception as e:
            print(f"Error getting user sessions: {e}")
            return []
    
    def get_session(self, user_email: str, session_id: str) -> Optional[Dict]:
        try:
            data = self._load_all()
            return data.get(user_email, {}).get(session_id)
        except Exception as e:
            print(f"Error getting session: {e}")
            return None
    
    def delete_session(self, user_email: str, session_id: str) -> bool:
        try:
            data = self._load_all()
            if user_email in data and session_id in data[user_email]:
                del data[user_email][session_id]
                self._save_all(data)
                return True
            return False
        except Exception as e:
            print(f"Error deleting session: {e}")
            return False
    
    def update_session_title(self, user_email: str, session_id: str, title: str) -> bool:
        try:
            data = self._load_all()
            if user_email in data and session_id in data[user_email]:
                data[user_email][session_id]["title"] = title
                data[user_email][session_id]["updated_at"] = datetime.now().isoformat()
                self._save_all(data)
                return True
            return False
        except Exception as e:
            print(f"Error updating session title: {e}")
            return False
