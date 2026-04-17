export type Role = {
  id: number;
  role_name: string;
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
  role?: Role;
  office?: Office;
};

export type SupplyRequest = {
  id: number;
  user_id: number;
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
