export type Role = {
  id: number;
  role_name: string;
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
};
