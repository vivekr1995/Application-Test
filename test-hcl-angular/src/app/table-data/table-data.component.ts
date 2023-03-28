import { Component, DefaultIterableDiffer, Input, OnInit, Output, EventEmitter } from '@angular/core'
import { MatTableDataSource } from '@angular/material/table'
import { User, UserColumns } from '../interface/userData'
import { UserService } from '../services/user.service'
import { MatSnackBar } from '@angular/material/snack-bar';
import { Sort } from '@angular/material/sort';

@Component({
  selector: 'table-data',
  templateUrl: './table-data.component.html',
  styleUrls: ['./table-data.component.scss'],
})
export class TableDataComponent {
  displayedColumns: string[] = UserColumns.map((col) => col.key)
  columnsSchema: any = UserColumns

  //Parent child interaction
  @Input() dataSource = new MatTableDataSource<User>();
  @Output() reset = new EventEmitter<boolean>();

  valid: any = {}
  is_disabled = false;

  sortedData: User[];

  constructor(private userService: UserService, public snackBar : MatSnackBar) {
    this.sortedData = this.dataSource.data.slice();
  }

  ngOnInit() {
    this.dataSource.filterPredicate = (data:
        {name: string}, filterValue: string) =>
        data.name.trim().toLowerCase().indexOf(filterValue) !== -1;
  }

  //Datatable content reset, child-parent interaction
  resetItem() {
    this.reset.emit(true);
  }

  //Add and edit table content
  editRow(row: User) {
    //New entry
    if (row.id === 0) {
        //Validating required data
        if(row.name == '' || row.zip == '' || row.amount == '' || row.qty == '' || row.item == '') {
            //Button disabled and showing alert message
            this.is_disabled = true;
            this.snackBar.open('The required fields could not Blank!', 'Required', {
                duration : 2000,
                panelClass : ['mat-toolbar', 'mat-warn']
            });
        } else {
            if(this.dataSource.data) {
                var is_unique = true;
                //Checking item already exists
                this.dataSource.data.forEach(element => {
                    if(element.id != 0 && element.item == row.item) {
                        is_unique = false;
                    }
                });

                if(is_unique) {
                    //Adding new entry
                    this.userService.addUser(row).subscribe((response: any) => {
                      if(response.success) {
                        this.snackBar.open('Data added successfully', 'Added', {
                          duration : 2000,
                          panelClass : ['mat-toolbar', 'mat-primary']
                        });

                        this.resetItem();
                      } else {
                        this.snackBar.open('Data add failed', 'Failed', {
                          duration : 2000,
                          panelClass : ['mat-toolbar', 'mat-warn']
                        });
                      }
                        
                    })
                    //Success message
                    
                } else {
                    //Showing error message
                    this.is_disabled = true;
                    this.snackBar.open('Item should be unique', 'Not Unique', {
                        duration : 2000,
                        panelClass : ['mat-toolbar', 'mat-warn']
                    });
                }
            }
        }
    } else {
        //Updating existing data
        this.userService.updateUser(row).subscribe((response: any) => {
          if(response.success) {
            this.snackBar.open('Data updated successfully', 'Updated', {
              duration : 2000,
              panelClass : ['mat-toolbar', 'mat-primary']
            });
            this.resetItem();
          } else {
            this.snackBar.open('Data update failed', 'Failed', {
              duration : 2000,
              panelClass : ['mat-toolbar', 'mat-warn']
            });
          }
        })
    }
  }

  //Single data delete
  removeRow(id: number) {
    this.userService.deleteUser(id).subscribe((response: any) => {
      if(response.success) {
        this.dataSource.data = this.dataSource.data.filter(
          (u: User) => u.id !== id,
        )
        this.snackBar.open('Data removed successfully', 'Deleted', {
            duration : 2000,
            panelClass : ['mat-toolbar', 'mat-primary']
        });
      } else {
        this.snackBar.open('Data remove failed', 'Failed', {
          duration : 2000,
          panelClass : ['mat-toolbar', 'mat-warn']
        });
      }
        
    })
  }

  //Data validity check
  inputHandler(e: any, id: number, key: string) {
    this.is_disabled = false;
    if (!this.valid[id]) {
      this.valid[id] = {}
    }
    this.valid[id][key] = e.target.validity.valid;
  }

  //Disabling submit button
  disableSubmit(id: number) {
    if (this.valid[id]) {
      return Object.values(this.valid[id]).some((item) => item === false)
    }
    return false
  }

  isAllSelected() {
    return this.dataSource.data.every((item) => item.isSelected)
  }

  isAnySelected() {
    return this.dataSource.data.some((item) => item.isSelected)
  }

  selectAll(event: any) {
    this.dataSource.data = this.dataSource.data.map((item) => ({
      ...item,
      isSelected: event.checked,
    }))
  }

  //Sorting data
  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.sortedData = data;
      return;
    }

    //Comparing each element
    this.sortedData = data.sort((a, b) => {
      const isAsc = sort.direction === 'asc';
      switch (sort.active) {
        case 'name':
          return compare(a.name, b.name, isAsc);
        case 'state':
          return compare(a.state, b.state, isAsc);
        case 'zip':
          return compare(a.zip, b.zip, isAsc);
        case 'amount':
          return compare(a.amount, b.amount, isAsc);
        case 'qty':
          return compare(a.qty, b.qty, isAsc);
        case 'item':
          return compare(a.item, b.item, isAsc);
        default:
          return 0;
      }
    });

    this.dataSource.data = this.sortedData;
  }

  //Searching for data
  applyFilter(event: Event) {
    const filterValue = (event.target as HTMLInputElement).value;
    this.dataSource.filter = filterValue.trim().toLowerCase();
  }

}

//Comparing data for search
function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
