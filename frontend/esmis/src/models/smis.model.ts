export type Permission = {
  id: number;
  name: string;
  description: string;
};

export type Role = {
  id: number;
  role_name: string;
  permissions?: Permission[];
};

export type Office = {
  id: number;
  office_name: string;
};

export type Category = {
  id: number;
  category_name: string;
};

export type Unit = {
  id: number;
  unit_name: string;
};

export type Supply = {
  stock_num: string;
  item_desc: string;
  quantity: number;
  status: string;
  remarks: string;
  category_id: number;
  unit_id: number;
  category?: Category;
  unit?: Unit;
};

export type User = {
  id: number;
  first_name: string;
  middle_initial?: string;
  last_name: string;
  email: string;
  role_id: number;
  office_id: number;
  has_custom_permissions?: boolean;
  role?: Role;
  office?: Office;
  permissions?: Permission[];
};

export type SupplyRequest = {
  id: number;
  user_id: number;
  batch_id?: string;
  supply_id: string;
  quantity_req: number;
  purpose: string;
  status: string;
  approved_by?: number;
  created_at: string;
  updated_at: string;
  user?: User;
  supply?: Supply;
  approver?: User;
};

export type Archive = {
  id: number;
  table_name: string;
  original_id: number;
  data: SupplyRequest;
  archived_by: number;
  archived_at: string;
  archiver?: User;
};

export type Notification = {
  id: number;
  user_id: number;
  office_id?: number;
  request_id?: number;
  action: string;
  message: string;
  read_at: string | null;
  created_at: string;
  updated_at: string;
  user?: User;
  office?: Office;
  supply_request?: SupplyRequest;
};

export type ChangePasswordPayload = {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
};

export type ForgotPasswordPayload = {
  email: string;
};

export type ResetPasswordPayload = {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
};

export type CheckResetTokenPayload = {
  email: string;
  token: string;
};

export type CheckResetTokenResponse = {
  message: string;
  expires_at: string;
  server_time: string;
};

export type GeneralResponse = {
  message: string;
};

export type AuthResponse = {
  user: User;
  permissions: string[];
  token: string;
};

export type ProfileResponse = {
  user: User;
  permissions: string[];
};

export type LoginCredentials = {
  email: string;
  password: string;
};

export type PaginatedResponse<T> = {
  current_page: number;
  data: T[];
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: { url: string | null; label: string; active: boolean }[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number;
  total: number;
};

export type NotificationFilters = {
  office_id?: string | number;
  from_date?: string;
  to_date?: string;
};

export type AdminAudit = {
  id: number;
  admin_id: number | null;
  admin_name: string | null;
  admin_role: string | null;
  action_type: string;
  target_id: string | null;
  target_type: string | null;
  target_name: string | null;
  old_values: any | null;
  new_values: any | null;
  description: string | null;
  ip_address: string | null;
  user_agent: string | null;
  performed_at: string;
  created_at: string;
  updated_at: string;
  admin?: User;
};

export type AuditFilters = {
  search?: string;
  action_type?: string;
  admin_id?: number;
  page?: number;
  limit?: number;
};
