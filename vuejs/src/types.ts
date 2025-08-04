export interface User {
  id: number
  name: string
  email: string
  roles: string[]
}

export interface Tokens {
  access_token: string
  refresh_token: string
}

export interface Leave {
  id:         number;
  user_id:    number;
  start_date: string;
  end_date:   string;
  reason:     string;
  status:     LeaveStatus;
  created_at: string;
  updated_at: string;
}

export type LeaveStatus = 'pending' | 'approved' | 'rejected'
