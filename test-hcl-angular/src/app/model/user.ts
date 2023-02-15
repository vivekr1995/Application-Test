export interface User {
  isSelected: boolean;
  id: number;
  name: string;
  state: string;
  zip: string;
  amount: string;
  qty: string;
  item: string;
  isEdit: boolean;
}

export const UserColumns = [
  {
    key: 'isSelected',
    type: 'isSelected',
    label: '',
  },
  {
    key: 'name',
    type: 'text',
    label: 'Name',
    required: true,
  },
  {
    key: 'state',
    type: 'text',
    label: 'State',
  },
  {
    key: 'zip',
    type: 'text',
    label: 'Zip',
  },
  {
    key: 'amount',
    type: 'text',
    label: 'Amount',
  },
  {
    key: 'qty',
    type: 'text',
    label: 'Qty',
  },
  {
    key: 'item',
    type: 'text',
    label: 'Item',
  },
  {
    key: 'isEdit',
    type: 'isEdit',
    label: '',
  },
];
