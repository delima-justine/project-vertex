import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Approved } from './approved';

describe('Approved', () => {
  let component: Approved;
  let fixture: ComponentFixture<Approved>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Approved]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Approved);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
