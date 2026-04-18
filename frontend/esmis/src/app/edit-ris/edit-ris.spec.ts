import { ComponentFixture, TestBed } from '@angular/core/testing';

import { EditRis } from './edit-ris';

describe('EditRis', () => {
  let component: EditRis;
  let fixture: ComponentFixture<EditRis>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [EditRis]
    })
    .compileComponents();

    fixture = TestBed.createComponent(EditRis);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
