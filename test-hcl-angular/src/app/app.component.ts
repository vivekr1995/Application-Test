import { Component, DefaultIterableDiffer, OnInit } from '@angular/core'
import { MatDialog } from '@angular/material/dialog'
import { MatTableDataSource } from '@angular/material/table'
import { ConfirmDialogComponent } from './confirm-dialog/confirm-dialog.component'
import { User, UserColumns } from './interface/userData'
import { UserService } from './services/user.service'
import { MatSnackBar } from '@angular/material/snack-bar';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
})
export class AppComponent {
  dataSource = new MatTableDataSource<User>();

  constructor(public dialog: MatDialog, private userService: UserService, public snackBar : MatSnackBar) {}

  ngOnInit() {
    this.resetData();
  }

  //Reseting data
  resetData() {
    this.userService.getUsers().subscribe((res: any) => {
      this.dataSource.data = res;
    })
  }

  //Showing field for new entry
  addRow() {
    const newRow: User = {
      id: 0,
      name: '',
      state: '',
      zip: '',
      amount: '',
      qty: '',
      item: '',
      isEdit: true,
      isSelected: false,
    }
    this.dataSource.data = [newRow, ...this.dataSource.data]
  }

  //Removing multiple selected data from table
  removeSelectedRows() {
    const users = this.dataSource.data.filter((u: User) => u.isSelected)
    this.dialog
      .open(ConfirmDialogComponent)
      .afterClosed()
      .subscribe((confirm) => {
        if (confirm) {
          this.userService.deleteUsers(users).subscribe(() => {
            this.dataSource.data = this.dataSource.data.filter(
              (u: User) => !u.isSelected,
            )
          })

          this.snackBar.open('Data removed successfully', 'Deleted', {
            duration : 2000,
            panelClass : ['mat-toolbar', 'mat-primary']
          });
        }
      })
  }

}
